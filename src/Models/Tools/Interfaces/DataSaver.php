<?php

namespace App\Models\Tools\Interfaces;


interface DataSaver
{
    public function add(Object $object): void;

    public function update(Object $object): void;

    public function delete(Object $object): void;
}
