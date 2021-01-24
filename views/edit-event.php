<?php
    if(!isset($_SESSION['user_id'])){
        header("Location: /login");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Organiza Dashboard</title>
    <?php
        include 'externals.php';
    ?>
    <link type="text/css" rel="stylesheet" href="/views/styles/admin.css" />
</head>
<body>
    <?php
        include_once __DIR__ . "/includes/admin-nav.php";
    ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-7 mx-auto mb-2">
                <h3><a href="/dashboard/events" class="btn btn-light mr-3"><i class="fa fa-arrow-left mr-2"></i>Back</a>Edit Event</h3>

                <div class="card">
                    <div class="card-body">
                        <form method="post" class="needs-validation" novalidate>
                            <div class="form-group">
                                <img class="image-display mb-3" id="image-display" /><br />
                                <label for="event_image" class="btn" style="border: solid 1px #ccc;">
                                    <input 
                                            id="event_image" 
                                            type="file" 
                                            accept="image/gif, image/jpeg, image/png"
                                            style="display:none"
                                            onchange="loadImage(event)"
                                             />
                                    <i class="fa fa-cloud-upload"></i> Upload Event Image
                                </label>
                                <small class="ml-2">Max: 100kb</small>
                            </div>
                            <div class="form-group">
                                <label for="title">Title*</label>
                                <input 
                                        type="text" 
                                        name="title" 
                                        id="title" 
                                        class="form-control" 
                                        placeholder="Title of Event" 
                                        required 
                                        />
                                <div class="valid-feedback"></div>
                                <div class="invalid-feedback">This field is required</div>
                            </div>


                            <div class="form-group">
                                <label for="description">Description*</label>
                                <textarea 
                                        name="description" 
                                        id="description" 
                                        class="form-control" 
                                        placeholder="Description of Event" 
                                        required
                                        rows="4" 
                                        ></textarea>
                                <div class="valid-feedback"></div>
                                <div class="invalid-feedback">This field is required</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Start Date*</label>
                                        <input 
                                                type="date" 
                                                name="start_date" 
                                                id="start_date" 
                                                class="form-control" 
                                                placeholder="Start date of the event" 
                                                required
                                                />
                                        <div class="valid-feedback"></div>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date">End Date*</label>
                                        <input 
                                                type="date" 
                                                name="end_date" 
                                                id="end_date" 
                                                class="form-control" 
                                                placeholder="End date of the event" 
                                                required
                                                />
                                        <div class="valid-feedback"></div>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_time">Start Time*</label>
                                        <input 
                                                type="time" 
                                                name="start_time" 
                                                id="start_time" 
                                                class="form-control" 
                                                placeholder="start time of the event" 
                                                required
                                                />
                                        <div class="valid-feedback"></div>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_time">End Time*</label>
                                        <input 
                                                type="time" 
                                                name="end_time" 
                                                id="end_time" 
                                                class="form-control" 
                                                placeholder="End time of the event" 
                                                required
                                                />
                                        <div class="valid-feedback"></div>
                                        <div class="invalid-feedback">This field is required</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">Address*</label>
                                <input 
                                        type="text" 
                                        name="address" 
                                        id="address" 
                                        class="form-control" 
                                        placeholder="Address of the event" 
                                        required 
                                        />
                                <div class="valid-feedback"></div>
                                <div class="invalid-feedback">This field is required</div>
                            </div>

                            <div class="form-group">
                                <button type="submit" id="submit" class="btn btn-primary btn-block">Edit Event</button>
                            </div>
                           
                        </form>
                    </div>
                </div>
            </div>
            

    </div>

    <script>

        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    if (form.checkValidity() === false) {
                        event.stopPropagation();
                    }else{
                        editEvent();
                    }
                    form.classList.add('was-validated');
    
                }, false);
                });
            }, false);
            })();


            
        function loadImage(event){
            let image = document.getElementById('image-display');
	        image.src = URL.createObjectURL(event.target.files[0]);
        }

        async function loadEventTypes(){
            await fetch('/api/event_types')
                    .then(response => response.json())
                    .then(eventTypes => {
                        let options = '';
                        let counter = 0;
                        for(let eventType in eventTypes){
                            if(counter == 0){
                                options += `<option selected value=${eventTypes[eventType].id}>${eventTypes[eventType].type}</option>`;
                            }else{
                                options += `<option value=${eventTypes[eventType].id}>${eventTypes[eventType].type}</option>`;
                            }
                            counter++;
                        }
                        $('#event_type').html(options);
                    })
                    .catch(error => {
                        console.log("Something went wrong");
                    })
        }

        async function getEventDetails(){
            let formData = {
                eventId: <?php echo $_GET['event']; ?>
            }

            await fetch('/api/user_event', {
                method: 'POST',
                cache: 'no-cache',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                data = data[0];
                $('#title').val(data.title);
                $('#description').html(data.description);
                $('#start_date').val(data.start_date);
                $('#start_time').val(data.start_time);
                $('#end_date').val(data.end_date);
                $('#end_time').val(data.end_time);
                $('#address').val(data.address);
                $('#image-display').prop('src', '/' + data.image);
                console.log(data);
            })
            .catch(error => {
                location.href = "/dashboard/events";
                console.log('Error');
            })
        }

        async function editEvent(){  
            let formData = new FormData();
            formData.append('title', $('#title').val());
            formData.append('eventType', $('#event_type').val());
            formData.append('description', $('#description').val());
            formData.append('startDate', $('#start_date').val());
            formData.append('endDate', $('#end_date').val());
            formData.append('startTime', $('#start_time').val());
            formData.append('endTime', $('#end_time').val());
            formData.append('address', $('#address').val());
            formData.append('image', document.getElementById('event_image').files[0]);
            formData.append('eventId', <?php echo $_GET['event']; ?>);

            $('#submit').prop('disabled', true).html('Loading...');          
            await fetch('/api/events/edit', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('data: ', data);
                if(data.success){
                    bootbox.alert(data.message, function(){
                        location.href = "/dashboard/events";
                    });
                }else{
                    bootbox.alert(data.message);
                }
            })
            .catch(error => {
                console.log('error: ', error);
            })
            $('#submit').prop('disabled', false).html('Edit Event');          


            // console.log($('#event_type').val());
        }

        $(document).ready(function(){
            loadEventTypes();
            getEventDetails();
        })

        
    </script>
</body>
</html>