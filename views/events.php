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
    <link type="text/css" rel="stylesheet" href="cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css" />

    <link type="text/css" rel="stylesheet" href="/views/styles/admin.css" />
</head>
<body>
    <?php
        include_once __DIR__ . "/includes/admin-nav.php";
    ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12 mb-2">
                <h3>Welcome, <?php echo (isset($_SESSION['first_name'])) ?  $_SESSION['first_name'] :  ''; ?></h3>
            </div>
            <div class="col-md-12 mb-3">
                <a href="/dashboard/events/create" class="btn btn-primary text-white"><i class="fa fa-plus mr-2"></i>Create an Event</a>
            </div>
            <div class="col-md-12">
                <h5>Your events</h5>
                <div class="card">
                    <div class="card-body table-responsive">
                        <table id="events" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Type(s)</th>
                                    <th>Address</th>
                                    <th>Starting</th>
                                    <th>Ending</th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>

    <script>
        $(document).ready(function() {
           getEvents();
        })
        
         async function getEvents(){
             await fetch('/api/user_events')
                    .then(response => response.json())
                    .then(data => {
                        console.log('data: ', data);

                        let tableRows = '';
                        let counter = 1;
                        for (let d in data){
                            let eventTypes = '';
                            for(let eventType in data[d].event_types){
                                eventTypes += `
                                    <span class="badge badge-primary">${ data[d].event_types[eventType] }</span>
                                `;
                            }
                            tableRows += `
                                <tr>
                                    <td>${ counter }</td>
                                    <td>${ data[d].title }</td>
                                    <td>${ data[d].description }</td>
                                    <td>${ eventTypes }</td>
                                    <td>${ data[d].address }</td>
                                    <td>${ data[d].start_date } ${ data[d].start_time }</td>
                                    <td>${ data[d].end_date } ${ data[d].end_time }</td>
                                    <td>
                                        <button type="button" class="btn edit-btn" onclick="editEvent(${ data[d].event_id })"><i class="fa fa-pencil"></i></button>
                                        <button type="button" class="btn delete-btn"  onclick="deleteEvent(${ data[d].event_id })"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                            counter++;
                        }

                        $('#events tbody').html(tableRows);
                    })

                    $('#events').DataTable();

         }

         async function _deleteEvent(eventId){
             let formData = {
                 eventId: eventId
             }
            await fetch('/api/events/delete', {
                method: 'POST',
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                console.log('xx: ', data);
                if(data.success){
                    bootbox.alert(data.message, function(){
                        window.location.reload();  
                    });
                }else{
                    bootbox.alert(data.message);
                }
            })
            .catch(error => {
                console.log('error: ', error);
            })
         }

         function editEvent(event){
             location.href = "/dashboard/events/edit?event=" + event;
         }

         function deleteEvent(event){
             bootbox.confirm({
                 size: "small",
                 message: "Are you sure you want to delete this?",
                 callback: function(data){
                    if(data){
                        _deleteEvent(event)
                    }
                 }
                });
         }

    </script>
</body>
</html>