<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <?php
        include 'externals.php';
    ?>
    <link type="text/css" rel="stylesheet" href="/views/styles/auth.css" />
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-7 left auth-bg">
                <h1 class="text-center text-white auth-text">Eventify</h1>
                <h4 class="text-center text-white">Login</h4>
            </div>

            <div class="col-md-5 right">
                <h2 class="mb-3">Login Here</h2>
                <form method = 'post' class="needs-validation" novalidate>
                    <div class="form-group">
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            class="form-control" 
                            placeholder="Email"
                            required />
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback">Email is required and must be valid</div>
                    </div>
        
                    <div class="form-group">
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-control" 
                            placeholder="Password"
                            required />
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback">Password is required</div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-danger btn-block form-control" id="submit">Login</button>
                    </div>
                </form>
    
                <p class="text-right"><a href="/signup">Yet to signup? Signup here</a></p>
            </div>
        </div>
    </div>

    <script>
        
        (function() {
        'use strict';
        let matchingPasswords = true;
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {

                event.preventDefault();
                if (form.checkValidity() === false || !matchingPasswords) {
                    event.stopPropagation();
                }else{
                    login();

                }
                form.classList.add('was-validated');

            }, false);
            });
        }, false);
        })();

        
        async function login(){          
            let formData = {
                email: $('#email').val(),
                password: $('#password').val()
            }
            $('#submit').prop('disabled', true).html('Loading...');
            await fetch('/api/login', {
                method: 'POST',
                cache: 'no-cache',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                $('#submit').prop('disabled', false).html('Signup');
                if(data.success){
                    location.href = "/dashboard/events";
                }
                console.log(data);
            })
            .catch(error => {
                console.log('Error');
                $('#submit').prop('disabled', false).html('Signup');
            })


        }
    </script>
</body>
</html>