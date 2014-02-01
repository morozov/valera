<?php
namespace Valera;
/**
 * Class Daemon
 * @package Valera
 */
class Daemon
{

    /**
     * @var string STDIN stream
     */
    static private $stdIn = '/dev/null';
    /**
     * @var string STDOUT stream
     */
    static private $stdOut = '/dev/null';
    /**
     * @var string STDERR stream
     */
    static private $stdErr = '/dev/null';
    /**
     * @var Path to pid file
     */
    static private $pidFile;

    /**
     * @var
     */
    static private $signalHandler;

    /**
     * @return int
     */
    public static function getPid()
    {
        return getmypid();
    }

    /**
     * @param $path
     */
    public static function setStdIn($path)
    {
        static::$stdIn = $path;
    }

    /**
     * @param $path
     */
    public static function setStdOut($path)
    {
        static::$stdOut = $path;
    }

    /**
     * @param $path
     */
    public static function setStdErr($path)
    {
        static::$stdErr = $path;
    }

    /**
     * @return string
     */
    public static function getStdIn()
    {
        return static::$stdIn;
    }

    /**
     * @return string
     */
    public static function getStdOut()
    {
        return static::$stdOut;
    }

    /**
     * @return string
     */
    public static function getStdErr()
    {
        return static::$stdErr;
    }

    /**
     * @throws \RuntimeException
     */
    protected static function lock()
    {
        $pidFile = static::getPidFile();
        if (static::isLocked()) {
            throw new \RuntimeException('Daemon already runniing');
        }
        file_put_contents($pidFile, static::getPid());
    }

    /**
     * @return bool
     */
    protected static function isLocked()
    {
        return file_exists(static::getPidFile());
    }

    /**
     * @return string
     */
    protected static function getPidFile()
    {
        $dir = sys_get_temp_dir();
        return static::$pidFile ? : $dir . DIRECTORY_SEPARATOR . 'valera_daemon.pid';
    }

    /**
     * @param callable $loopHandler
     * @throws \RuntimeException
     */
    public static function run(callable $loopHandler)
    {
        //Prevent running two copies of the daemon
        if (static::isLocked()) {
            throw new \RuntimeException('Daemon is already running');
        }
        //Unlock pidfile on shutdown
        register_shutdown_function(
            function () {
                clearstatcache();
                $pidFile = static::getPidFile();
                if (file_exists($pidFile)) {
                    unlink($pidFile);
                }
            }
        );
        $pid = pcntl_fork();
        if ($pid == -1) {
            exit("Could not fork the child process");
        }
        // If $pid is set to a process ID, then we must be the parent, and should exit.
        if ($pid) {
            exit(0);
        }
        /** As the parent has exited above, the following code is now executed
         * solely by the child process.
         * We detach from the TTY (terminal) by becoming the "session leader"
         * (instead of the TTY being leader. This starts a new POSIX session)
         */
        if (posix_setsid() === -1) {
            exit("Could not become the session leader");
        }
        //Create grandchild
        $pid = pcntl_fork();

        if ($pid == -1) {
            exit("Could not fork child process into grandchild");
        }

        if ($pid) {
            exit(0);
        }

        /**
         * Exit the child process, leaving only the grandchild beyond this point.
         * Now to finally dissociate from the TTY and run in the background, we
         * need to close our input and output streams to it.
         */
        if (!fclose(STDIN)) {
            exit('Could not close STDIN');
        }

        if (!fclose(STDERR)) {
            exit('Could not close STDERR');
        }

        if (!fclose(STDOUT)) {
            exit('Could not close STDOUT');
        }

        /**
         * Reopen STDIN, STDOUT, STDERR streams
         */
        $STDIN = fopen(static::$stdIn, 'r');

        $STDOUT = fopen(static::$stdOut, 'w');

        $STDERR = fopen(static::$stdErr, 'wb');

        static::lock();
        static::bindHandlers();
        // We are now fully detached from our TTY
        while ($loopHandler()) {
            usleep(20);
        }
        exit(0);
    }

    /**
     * Bind handler to signals
     */
    static public function bindHandlers()
    {
        $handler = static::getSignalHandler();
        //SIGKILL is not catchable;
        pcntl_signal(SIGTERM, $handler);
        pcntl_signal(SIGINT, $handler);

    }

    /**
     * @param callable $handler
     */
    static public function setSignalHandler(callable $handler)
    {
        static::$signalHandler = $handler;
    }

    /**
     * @return int
     */
    static public function getSignalHandler()
    {
        return static::$signalHandler?: SIG_IGN;
    }
}
