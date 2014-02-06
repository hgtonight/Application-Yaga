<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo Wrap($this->Title(), 'h1');

echo $this->ConfigurationModule->ToString();