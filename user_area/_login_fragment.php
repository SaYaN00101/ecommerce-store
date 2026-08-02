<?php
// Login form fragment — expects $message (optional) and CSRF helper to be available
?>
<?php if (!empty($message)) echo $message; ?>

<form method="post" action="">
    <?php csrf_input(); ?>

    <div class="form-outline mb-4 col-12 col-md-6 mx-auto">
        <label for="login_input" class="form-label">Email or Username</label>
        <input type="text" name="login_input" id="login_input" class="form-control" required />
    </div>

    <div class="form-outline mb-4 col-12 col-md-6 mx-auto">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required />
    </div>

    <div class="form-outline mb-4 col-12 col-md-6 mx-auto text-center">
        <input type="submit" name="login" class="btn btn-primary px-4" value="Login" />
    </div>

    <div class="text-center">
        <p>Don't have an account? <a href="user_registration.php">Register here</a></p>
    </div>
</form>
