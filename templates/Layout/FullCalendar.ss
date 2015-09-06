<div class="document-overlay">
	<% if $LoadAnimation %>
		$LoadAnimation
	<% else %>
        <img src="full-calendar/images/pre-loading.gif" alt="loading"/>
	<% end_if %>
</div>

<h1>Download <a href="">.ics</a></h1>

<div id="calendar" data-root-url="$getDocumentRoot.Link"></div>

<% include Fancybox %>
