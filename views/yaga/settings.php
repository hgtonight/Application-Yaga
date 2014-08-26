<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013-2014 Zachary Doll */

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

?>

<div class="Footer">
	<?php
	echo Wrap(T('Feedback'), 'h3');
	?>
	<div class="Aside Box">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="hosted_button_id" value="W277NKS7JF9FW">
      <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
      <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
	</div>
	<?php
	echo Wrap('Find this plugin helpful? Want to support a freelance developer?<br/>Click the donate button to buy me a beer. :D', 'div', array('class' => 'Info')); 
	echo Wrap('Confused by something? <strong><a href="http://vanillaforums.org/post/discussion?AddonID=1178">Ask a question</a></strong> about Yaga on the official <a href="http://vanillaforums.org/discussions" target="_blank">Vanilla forums</a>.', 'div', array('class' => 'Info'));
	?>
</div>