<!DOCTYPE html>
<html lang="en">

    <head>

        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Nigeria Health Facility Registry</title>
        <link rel="stylesheet" href="<?php echo e(asset('auth/styles/login.styles.css')); ?>" />
    </head>

    <body>
        <div class="container">
            <div class="wrapper">
                <div class="sidebar">
                    <img src="<?php echo e(asset('auth/SideBar.svg')); ?>" alt="Reset Password Illustration" />
                </div>

                <div class="form-container">
                    <div>
                        <a href="<?php echo e(env('FRONTEND_URL')); ?>">
                            <img src="<?php echo e(asset('auth/new-logo.svg')); ?>" alt="Logo" class="logo" />
                        </a>
                    </div>

                    <h1 class="heading">Nigeria Health Facility Registry (HFR)</h1>
                    <p class="subtitle">Login to Access your Dashboard</p>
                    <p class="subtitle" style="margin-top: -6px;">
                        <a href="<?php echo e(env('FRONTEND_URL')); ?>" style="text-decoration: none; color:grey">Go to Home Page</a>
                    </p>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form class="form" method="POST" action="<?php echo e(route('login')); ?>" aria-label="<?php echo e(__('Login')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input id="email" type="email" class="form-control" name="email"
                                value="<?php echo e(old('email')); ?>" placeholder="E-mail" autofocus>

                            <?php if($errors->has('email')): ?>
                                <span class="help-block" style="color: red">
                                    <?php echo e($errors->first('email')); ?>

                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="new-password">Password</label>
                            <input id="password" type="password" class="form-control" name="password"
                                placeholder="Password">

                            <?php if($errors->has('password')): ?>
                                <span class="help-block" style="color: red">
                                    <?php echo e($errors->first('password')); ?>

                                </span>
                            <?php endif; ?>
                        </div>

                        <a class="forgot-password" href="<?php echo e(route('password.request')); ?>">
                            Forgot Password
                        </a>

                        <div>
                            <button type="submit" class="submit-btn">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


        <?php if(session('success')): ?>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        title: 'Success!',
                        text: '<?php echo e(session('success')); ?>',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
            </script>
        <?php endif; ?>

        <?php if($errors->has('email')): ?>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        title: 'Error!',
                        text: '<?php echo e($errors->first('email')); ?>',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
            </script>
        <?php endif; ?>

        <?php if($errors->has('password')): ?>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        title: 'Error!',
                        text: '<?php echo e($errors->first('password')); ?>',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
            </script>
        <?php endif; ?>



    </body>

</html>
<?php /**PATH C:\Users\DANIEL\Desktop\ehealth4everyone\hfr\backend_v2\resources\views/auth/login.blade.php ENDPATH**/ ?>