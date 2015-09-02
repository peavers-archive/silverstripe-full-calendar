describe('console html content', function() {

	beforeEach(function() {
		jasmine.getFixtures().fixturesPath = '';
		loadFixtures("specRunner.html");
	});

	beforeEach(function() {
		jasmine.Ajax.requests.when = function (url) {
			return this.filter("/jquery/ajax")[0];
		};
		jasmine.Ajax.install();
	});

	it('specRunner html', function() {
		expect($("h2")).toBeInDOM();
		expect($("h2")).toContainText("this is index page");
	});

	it("ajax mock", function() {
		var doneFn = jasmine.createSpy("success");

		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(args) {
			if (this.readyState == this.DONE) {
				doneFn(this.responseText);
			}
		};

		xhr.open("GET", "/some/cool/url");
		xhr.send();

		expect(jasmine.Ajax.requests.mostRecent().url).toBe('/some/cool/url');
		expect(doneFn).not.toHaveBeenCalled();

		jasmine.Ajax.requests.mostRecent().response({
			"status": 200,
			"contentType": 'text/plain',
			"responseText": 'awesome response'
		});

		expect(doneFn).toHaveBeenCalledWith('awesome response');
	});

	it("jquery ajax success with getResponse", function() {
		var result;
		getResponse().then(function(data){
			result = data;
		});

		jasmine.Ajax.requests.when("/jquery/ajax").response({
			"status": 200,
			"contentType": 'text/plain',
			"responseText": 'data from mock ajax'
		});

		expect(result).toEqual('data from mock ajax');
	});

	it("jquery ajax error", function() {
		var status;
		$.get("/jquery/ajax").error(function(response) {
			status = response.status;
		});

		jasmine.Ajax.requests.when("/jquery/ajax").response({
			"status": 400
		});

		expect(status).toEqual(400);
	});

	afterEach(function() {
		jasmine.Ajax.uninstall();
	});
})
