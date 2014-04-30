<?php

namespace Valera\Result;

interface ResultInterface
{
    public function accept(Visitor $visitor);
}
