describe('Unit test for function.js',function(){
	//beforeEach(function() {
	//	jasmine.getFixtures().fixturesPath = 'D\:/WebDev/karma-jasmine-jquery/full-calendar/javascript/test/';
	//	loadFixtures('index.html');
	//});
	beforeEach(function() {
		jasmine.Ajax.requests.when = function (url) {
			return this.filter("/jquery/ajax")[0];
		};
		jasmine.Ajax.install();

	});
	it('Testing var rootUrl',function(){
		expect(rootUrl).toBe();
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
		//expect($('#calendar').fullCalendar()).toHaveBeenCalled();
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

	it('Testing for calendarSettings',function(){
		var calendar = jasmine.createSpy($('#calendar'));
		expect(calendar.columnFormat="abc").toEqual("abc");
		expect(calendar.defaultView="abc").toEqual("abc");
		expect(calendar.firstDay="abc").toEqual("abc");
		expect(calendar.events="abc").toEqual("abc");
		var eventClick = spyOnEvent($('#calendar'),'click');
		var event={title:"abc"}
		$('#calendar').click(event);
		//var title = jasmine.createSpy($('.title'));
		expect($('.title').length).toBe(0);
	});
	afterEach(function() {
		jasmine.Ajax.uninstall();
	});
});
