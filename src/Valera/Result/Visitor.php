<?php

namespace Valera\Result;

interface Visitor
{
    public function visit(ResultInterface $result);
    public function visitSuccess(Success $result);
    public function visitFailure(Failure $result);
}
