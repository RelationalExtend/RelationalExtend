<div style="padding: 50px; text-align: center; background-color: #dcdcdc;">

<?php foreach($messages as $message) { ?>
	<div class="alert"><?php echo($message); ?></div>
<?php } ?>	

    <form class="form-inline" method="post" action="<?php echo($form_action."login"); ?>">
        <input type="text" class="input-small" placeholder="Email / Username" name="username">
        <input type="password" class="input-small" placeholder="Password" name="password">

        <!--
        <label class="checkbox">
            <input type="checkbox" name="remember_me"> Remember me
        </label>
        -->
        
        <button type="submit" class="btn">Sign in</button>
    </form>
</div>