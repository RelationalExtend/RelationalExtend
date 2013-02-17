<div style="padding: 50px; text-align: center; background-color: #dcdcdc;">
    <form class="form-inline" method="post" action="<?php echo(Uri::base()."cms/cms/login"); ?>">
        <input type="text" class="input-small" placeholder="Email / Username" name="username">
        <input type="password" class="input-small" placeholder="Password" name="password">

        <label class="checkbox">
            <input type="checkbox" name="remember_me"> Remember me
        </label>

        <p style="padding-top:10px; margin-right:230px;">
            <button type="submit" class="btn">Sign in</button>
        </p>
    </form>
</div>