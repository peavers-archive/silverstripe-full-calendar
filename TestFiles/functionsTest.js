function getResponse(){
	return $.get("/jquery/ajax").success(function(data) {
		return data;
	});
}

function sayHello(){
	console.log("hello world");
	return "hello";
}
