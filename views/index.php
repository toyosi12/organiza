<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Organiza</title>
    <?php
        include 'externals.php';
    ?>
    <link type="text/css" rel="stylesheet" href="/views/styles/index.css" />
</head>
<body>
    <?php
        include_once __DIR__ . "/includes/nav.php";
    ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 _carousel-container">
                <div class="_carousel">
                    <h2 class="text-white text-center mb-5">Organizing your events</h2>
                    <div class="buttons">
                        <a class="btn register" href="/signup">Register</a>
                        <a class="btn login text-white" href="/login">Login</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4>Recent Events</h4>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-search text-primary"></i></span>
                    </div>
                    <input type="text" class="form-control" placeholder="Search Events..." id="search">
                  </div>
                <p id="loading"></p>
            </div>
            
        </div>
        <div class="row" id="events-container">

        </div>
        <div class="row mt-3">
            <div class="col-md-12" id="paginator">

            </div>
        </div>
    </div>

    

    <script>
        
        async function loadEvents(){
            $('#loading').html("Loading...");
            await fetch('/api/events')
                    .then(response => response.json())
                    .then(events => {
                        if(events.length === 0){
                            $('#events-container').html("<div class='col-md-12'>No event found</div>");
                            return;
                        }
                        let eventsContent = '';
                        let badgeText = '';
                        let specialClass = '';
                        for(let event in events){
                            if(events[event].event_types.includes('Recruiting Mission')){ 
                                badgeText = 'Recruiting Mission';
                                specialClass = "premium";
                            }else if(events[event].event_types.includes('Leap')){
                                badgeText = 'Leap';
                                specialClass = "premium";
                            }else if(events[event].event_types.includes('Hackathon')){
                                badgeText = 'Hackathon';
                                specialClass = "premium";
                            }else if(events[event].event_types.includes('Premium-Only Webinar')){
                                badgeText = 'Premium-Only Webinar';
                            }else{
                                badgeText = "nil";
                                specialClass = "";
                            }
                            let imagePath = "/" + events[event].image ;
                            eventsContent +=  `
                                <div class="col-md-3 mb-4" onclick="navigate(${ events[event].event_id })">
                                    <div class="card">
                                        <img src=${ imagePath } class="card-img-top" style="object-fit: cover" onerror="this.onerror=null; this.src = '/views/images/auth-bg.jpg'" />
                                        <div class="card-body ${specialClass}">
                                            <h5 class="card-title">${events[event].title}</h5>
                                            <span class="badge badge-premium ${(specialClass !== "premium") ? "hidden" : ""}">${badgeText}</span>
                                            <p class="card-text date">${events[event].start_date + " " + events[event].start_time}</p>
                                        </div>
                                    </div>
                                </div>
                          `
                        }
                      $('#events-container').html(eventsContent);
                      $('#paginator').html(`
                        <nav>
                            <ul class="pagination">
                                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
                      `);
                    })
                    .catch(error => {
                        console.log("Something went wrong");
                    })

                    $('#loading').html("");

        }
        $(document).ready(function(){
            loadEvents();
        })

        function navigate(eventId){
            location.href = "/event?event=" + eventId;
        }

        $("#search").keyup(function(event) {
            if (event.keyCode === 13) {
                if($('#search').val() === ""){
                    loadEvents();
                }else{
                    searchEvents();

                }
            }
        });

        async function searchEvents(){
            $('#loading').html('Loading...');
            let formData = new FormData();
            formData.append('searchText', $('#search').val());
            await fetch('/api/events/search', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(events => {
                $('#loading').html('');
                console.log(events);
                let eventsContent = '';
                let badgeText = '';
                let specialClass = '';
                if(events.length === 0){
                    $('#events-container').html("<div class='col-md-12'>No event found</div>");
                }else{
                    for(let event in events){
                        if(events[event].event_types.includes('Recruiting Mission')){ 
                            badgeText = 'Recruiting Mission';
                            specialClass = "premium";
                        }else if(events[event].event_types.includes('Leap')){
                            badgeText = 'Leap';
                            specialClass = "premium";
                        }else if(events[event].event_types.includes('Hackathon')){
                            badgeText = 'Hackathon';
                            specialClass = "premium";
                        }else{
                            badgeText = "nil";
                            specialClass = "";
                        }
                        let imagePath = "/" + events[event].image ;
                        eventsContent +=  `
                            <div class="col-md-3 mb-4" onclick="navigate(${ events[event].event_id })">
                                <div class="card">
                                    <img src=${ imagePath } class="card-img-top" style="object-fit: cover" onerror="this.onerror=null; this.src = '/views/images/auth-bg.jpg'" />
                                    <div class="card-body ${specialClass}">
                                        <h5 class="card-title">${events[event].title}</h5>
                                        <span class="badge badge-premium ${(specialClass !== "premium") ? "hidden" : ""}">${badgeText}</span>
                                        <p class="card-text date">${events[event].start_date + " " + events[event].start_time}</p>
                                    </div>
                                </div>
                            </div>
                        `
                    }
                    $('#events-container').html(eventsContent);
                    $('#paginator').html(`
                        <nav>
                            <ul class="pagination">
                                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
                      `);

                }
            })
            .catch(error => {
                console.log("Something went wrong");
            })
        }
        
    </script>
</body>
</html>