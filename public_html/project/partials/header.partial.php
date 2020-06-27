<?php
require_once (__DIR__."/../includes/common.inc.php");
$logged_in = Common::is_logged_in(false);
?>
<nav>
    <ul>
        <?php if($logged_in):?>
        <li>
            <a href="<?php echo Common::url_for("home");?>">Home</a>
        </li>
        <li>
            <a href="<?php echo Common::url_for("game");?>">Game</a>
        </li>
        <?php endif; ?>
        <?php if(!$logged_in):?>
        <li>
            <a href="<?php echo Common::url_for("login");?>">Login</a>
        </li>
        <li>
            <a href="<?php echo Common::url_for("register");?>">Register</a>
        </li>
        <?php else:?>
        <li>
            <a href="<?php echo Common::url_for("logout");?>">Logout</a>
        </li>
        <?php endif; ?>
    </ul>
</nav>
<div id="messages">
    <?php $flash_messages = Common::getFlashMessages();?>
    <?php if(isset($flash_messages) && count($flash_messages) > 0):?>
        <?php foreach($flash_messages as $msg):?>
        <div class="<?php echo Common::get($msg, "type");?>"><?php
            echo Common::get($msg, "message");
            //We have the opening and closing tags right after/before the div tags to remove any whitespace characters
        ?></div>
        <?php endforeach;?>
    <?php endif;?>
</div>