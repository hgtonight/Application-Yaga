<?php if (!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

echo Wrap($this->Title(), 'h1');

echo Wrap(Wrap(T('Yaga.Transport.Desc'), 'div'), 'div', array('class' => 'Wrap'));
echo Wrap(Anchor(T('Yaga.Transport'), 'yaga', array('class' => 'SmallButton')), 'div', array('class' => 'Wrap'));
