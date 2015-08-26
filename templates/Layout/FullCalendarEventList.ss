<main id="main-content" role="main" itemprop="mainContentOfPage">

    <h1 id="page-title" itemprop="name">$Title.XML</h1>

    <div class="article-list">
		<% loop getEvent %>
            <article itemscope itemtype="http://schema.org/Article">
                <h2 itemprop="headline"><a href="$Link" itemprop="url">$Title</a></h2>

                <div class="description" itemprop="description">
                    <p>$ShortDescription</p>
                </div>

                <time itemprop="datePublished">$StartDate.Format(l jS F Y) - $EndDate.Format(l jS F Y)</time>
            </article>
		<% end_loop %>
    </div>

    <div class="pagination">
		<% with $getEvent %>
			<% if $MoreThanOnePage %>
				<% if $NotFirstPage %>
                    <a title="previous" href="$PrevLink" class="inactive">&lt;</a>
				<% end_if %>

				<% loop $PaginationSummary(4) %>
					<% if $CurrentBool %>
                        <a disabled="disabled" class="active">$PageNum</a>
					<% else %>
						<% if $Link %>
                            <a class="inactive" title="View page $PageNum of results" href="$Link">$PageNum</a>
						<% else %>
                            <a disabled="disabled">...</a>
						<% end_if %>
					<% end_if %>
				<% end_loop %>
				<% if $NotLastPage %>
                    <a title="next" href="$NextLink" class="inactive">&gt;</a>
				<% end_if %>
			<% end_if %>
		<% end_with %>
    </div>

</main>
