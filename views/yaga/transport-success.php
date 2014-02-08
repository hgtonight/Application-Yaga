<?php if (!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

$TransportType = $this->Data('TransportType');
$Filename = $this->Data('TransportPath');

echo Wrap($this->Title(), 'h1');
echo Wrap(Wrap(sprintf(T("Yaga.$TransportType.Success"), $Filename), 'div'), 'div', array('class' => 'Wrap'));
echo Wrap(Anchor(T('Yaga.Transport.Return'), 'yaga/settings'), 'div', array('class' => 'Wrap'));
