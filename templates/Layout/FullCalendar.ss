<div class="document-overlay">
	<% if $LoadAnimation %>
		$LoadAnimation
	<% else %>
        <img src="full-calendar/images/pre-loading.gif" alt="loading"/>
	<% end_if %>
</div>

<div id="calendar" data-root-url="$getDocumentRoot.Link"></div>

<a class="button-download" href=$CalFile.Filename>Download calendar <i class="fa fa-download"></i></a>

<% include Fancybox %>
