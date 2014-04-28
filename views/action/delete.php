<?php if(!defined('APPLICATION')) exit();

$ActionName = $this->Data('ActionName');
$OtherActions = $this->Data('OtherActions', NULL);

echo Wrap($this->Data('Title'), 'h1');

echo $this->Form->Open();
echo $this->Form->Errors();

echo Wrap(
        $this->Form->Checkbox('Move', sprintf(T('Yaga.Action.Move'), $ActionName)) . ' ' . $this->Form->DropDown('ReplacementID', $OtherActions), 'div', array('class' => 'Info'));
echo Wrap(
        sprintf(T('Are you sure you want to delete this %s?'), $ActionName . ' ' . T('Yaga.Action')) .
        Wrap(
                $this->Form->Button('OK', array('class' => 'Button Primary')) .
                $this->Form->Button('Cancel', array('type' => 'button', 'class' => 'Button Close')), 'div', array('class' => 'Buttons Buttons-Confirm')
        ), 'div', array('class' => 'Info'));

echo $this->Form->Close();
