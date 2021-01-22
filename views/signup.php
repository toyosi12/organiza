<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
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
                <h4 class="text-center text-white">Signup</h4>
            </div>

            <div class="col-md-5 right">
                <h3 class="mb-3">Signup Here</h3>
                <form method = 'post' class="needs-validation" novalidate>
                    <div class="form-group">
                        <input 
                                type="text" 
                                name="first_name" 
                                id="first_name" 
                                class="form-control" 
                                placeholder="First Name*" 
                                required
                                 />
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback">First name is required</div>
                    </div>
        
                    <div class="form-group">
                        <input 
                                type="text" 
                                name="last_name" 
                                id="last_name" 
                                class="form-control" 
                                placeholder="Last Name*" 
                                required
                                 />
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback">Last name is required</div>
                    </div>
    
                    <div class="form-group">
                        <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                class="form-control" 
                                placeholder="Email*" 
                                required
                                 />
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback">Email is required and must be valid</div>
                    </div>
    
                    <div class="form-group">
                        <input 
                                type="text" 
                                name="phone" 
                                id="phone" 
                                class="form-control" 
                                placeholder="Phone*" 
                                required
                                 />
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback">Phone is required</div>
                    </div>
                    
                    <div class="form-group">
                        <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="form-control" 
                                placeholder="Password*" 
                                required 
                                oninput="checkMatchingPasswords()"
                                min="6"
                                 />
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback">Password must be at least 6 characters</div>
                    </div>
    
                    <div class="form-group">
                        <input 
                                type="password" 
                                name="confirm_password" 
                                id="confirm_password" 
                                class="form-control" 
                                placeholder="Confirm Password*" 
                                required 
                                oninput="checkMatchingPasswords()"
                                 />
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback" id="confirm-password-feedback">This field is required</div>
                        <div><small class="text-danger" id="matching-passwords"></small></div>
                    </div>
        
                    <div class="form-group">
                        <button type="submit" class="btn btn-danger btn-block form-control" id="submit">Signup</button>
                    </div>
                </form>
                <p class="text-right"><a href="/login">Already signed up? Login here</a></p>
            </div>
        </div>
    </div>

    <script>
        
        let matchingPasswords = true;
        
            (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    
                    checkMatchingPasswords();
    
                    event.preventDefault();
                    if (form.checkValidity() === false || matchingPasswords === false) {
                        event.stopPropagation();
                    }else{
                        signup();

                    }
                    form.classList.add('was-validated');
    
                }, false);
                });
            }, false);
            })();
            
            

            function checkMatchingPasswords(){
                if($('#confirm_password').val() !== $('#password').val()){
                        $('#matching-passwords').html("Passwords do not match");
                        matchingPasswords = false;
                    }else{
                        $('#matching-passwords').html("");
                        matchingPasswords = true;
                    }
                
                if($('#password').val().length < 6){
                   // $('#matching-passwords').html("Password must not be less than 6 characters");
                    matchingPasswords = false;
                }else{
                    matchingPasswords = true;
                }

            }

            async function signup(){
                let formData = {
                firstName: $('#first_name').val(),
                lastName: $('#last_name').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                password: $('#password').val(),
                confirmPassword: $('#confirm_password').val(),
                }
                $('#submit').prop('disabled', true).html('Loading...');
                await fetch('/api/signup', {
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