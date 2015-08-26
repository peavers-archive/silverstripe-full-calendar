<div id="fancy-box">


    <div class="event-header"></div>

    <!-- AddThisEvent button -->
    <% if $AddEvents %>
    <div title="Add to Calendar" class="addthisevent">
        <i class="fa fa-calendar"></i>
        <span class="tips">Add to Calendar</span>
        <span class="start "></span>
        <span class="end"></span>
        <span class="timezone">Europe/Paris</span>
        <span class="title"></span>
        <span class="description"></span>
        <span class="location">Location of the event</span>
        <span class="date_format">MM/DD/YYYY</span>
        <span class="all_day_event">true</span>
   </div>
    <% end_if %>

    <div class="event-dates">
        <p><span class="event-start-date"></span> - <span class="event-end-date"></span></p>
    </div>

    <div class="event-content"></div>
    <div class="event-button">
        <p><a href="#">More details</a></p>
    </div>

</div>