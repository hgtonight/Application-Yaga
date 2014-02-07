<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo Wrap($this->Title(), 'h1');

echo $this->ConfigurationModule->ToString();

echo Wrap(T('Yaga.Transport'), 'h3');

echo Wrap(T('Yaga.Transport.Desc'), 'div', array('class' => 'Wrap'));

echo Wrap(
        Anchor(
                T('Import'),
                'yaga/import',
                array('class' => 'SmallButton')
                ) .
        Anchor(
                T('Export'),
                'yaga/export',
                array('class' => 'SmallButton')),
        'div',
        array(
            'class' => 'Wrap')
        );