<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Organiza</title>
    <?php
        include 'externals.php';
    ?>
    <link type="text/css" rel="stylesheet" href="/views/styles/event.css" />
</head>
<body id="body">
    <?php
        include_once __DIR__ . "/includes/nav.php";
    ?>

    <div class="modal fade" id="applyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apply for this event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body">
                    <form method="post" class="needs-validation" novalidate>
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" id="first_name" class="form-control" required />
                            <div class="valid-feedback"></div>
                            <div class="invalid-feedback">This field is required</div>
                        </div>
            
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" id="last_name" class="form-control" required />
                            <div class="valid-feedback"></div>
                            <div class="invalid-feedback">This field is required</div>
                        </div>
            
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="email" class="form-control" required />
                            <div class="valid-feedback"></div>
                            <div class="invalid-feedback">This field is required and must be a valid email</div>
                        </div>
            
                        <div class="form-group">
                            <button type="submit" id="submit-btn" class="btn btn-primary btn-block">Apply</button>
                        </div>

                    </form>
                    
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="subscribeModal" tabindex="0" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body">
                    <p>Sorry, this is a Premium-only webinar</p>
                    <a href="/dashboard/buy-membership" class="btn btn-primary btn-block">Buy a membership plan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto" id="image-container">
                
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-8 mx-auto" id="info">

            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-8 mx-auto" id="apply"></div>
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
                    applyToEvent();

                }
                form.classList.add('was-validated');

            }, false);
            });
        }, false);
        })();


        $(document).ready(function(){
            getEventDetails();
        })

        async function getEventDetails(){
            let formData = {
                eventId: <?php echo $_GET['event']; ?>
            }

            $('#image-container').html("Loading...");
            
            await fetch('/api/event_details', {
                method: 'POST',
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                data = data[0];
                let image = '/' + data.image;
                $('#image-container').html(`
                    <img src=${ image } alt=${ data.title } />
                `);
                let badge = '';
                for(let eventType in data.event_types){
                    badge += `
                        <span class="badge badge-primary">${ data.event_types[eventType] }</span>
                    `;
                }

                $('#info').html(`
                    <h3> ${data.title} </h3>
                    <p> ${data.description} </p>
                    <p>${ badge }</p>
                    <p><i class="fa fa-map-marker"></i>  ${ data.address }</p>
                    <p><i class="fa fa-calendar"></i>  ${ data.start_date + " " + data.start_time + " - " + data.end_date + " " + data.end_time }</p>
                `);

                $('#apply').html(`
                    <button type="button" class="btn btn-primary btn-block text-white" id="apply-btn" onclick="apply(${ data.id }, '${ data.event_types }')">Apply for this event</button>
                `);
            })
            .catch(error => {
                console.log('error: ', error);
            })
        }
    let eventId;
    function apply(_eventId, _eventTypes){
        eventTypesArray = _eventTypes.split(',');
        if(eventTypesArray.includes('Premium-Only Webinar')){
            $('#subscribeModal').modal();
        }else{
            $('#applyModal').modal();

        }
        eventId = _eventId;
    }

    async function applyToEvent(){
        $('#submit-btn').prop('disabled', true).html('Loading...');
        let formData = new FormData();
            formData.append('firstName', $('#first_name').val());
            formData.append('lastName', $('#last_name').val());
            formData.append('email', $('#email').val());
            formData.append('eventId', eventId);

            await fetch('/api/events/attend', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                    $('#applyModal').modal('hide');
                    bootbox.alert(data.message);
                    $('#submit-btn').prop('disabled', false).html('Apply');
                    $('#first_name').val('');
                    $('#last_name').val('');
                    $('#email').val('');
                })
            .catch(error => {
                $('#submit-btn').prop('disabled', false).html('Apply');
                console.log('error: ', error);
            })
    }

    </script>
</body>
</html>