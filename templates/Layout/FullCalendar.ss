$Title

<h1>$getDocumentRoot.Link</h1>

$Content

<div class="document-overlay">
    <% if $LoadAnimation %>
        $LoadAnimation
    <% else %>
        <img src="full-calendar/images/pre-loading.gif" alt="loading"/>
    <% end_if %>
</div>

<div id="calendar" data-root-url="$getDocumentRoot.Link"></div>

<% include Fancybox %>