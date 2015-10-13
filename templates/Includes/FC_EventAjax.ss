<div class="event-header {$EventColor} {$TextColor}">
    <span class="event-title">$Title</span>
</div>

<div class="event-dates">
    <span class="event-start-date">$StartDate.Format('F j Y g:i a')</span> - <span
        class="event-end-date">$EndDate.Format('F j Y g:i a')</span>
</div>

<div class="event-download">
    <a href="$CalFileURL">Download .ics file</a>
</div>

<div class="event-content">
    <span class="event-description">$ShortDescription</span>
</div>

<% if $Content %>
    <div class="event-button">
        <a href="$Link">More details</a>
    </div>
<% end_if %>
