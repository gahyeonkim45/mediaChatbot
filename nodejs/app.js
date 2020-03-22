var express = require('express');
var https = require('https');
var http = require('http');
var fs = require('fs');
var pythonshell = require('python-shell');
var bodyParser = require('body-parser');
var mysql = require('mysql');
var answerfile = require('./answer.js');
var cookieParser = require('cookie-parser');
var session = require('express-session');
var cors = require('cors');
var MemoryStore = require('session-memory-store')(session);

//express
var app = express();
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());
app.use(cookieParser());
app.use(cors({credentials: true, origin: true}));

// https 
var port1 = 80;  
var port2 = 443;

var options = { key: fs.readFileSync('/etc/ssl/www_mediachatbotproj_tk.pem'), 
cert: fs.readFileSync('/etc/ssl/www_mediachatbotproj_tk.crt')};

http.createServer(app).listen(port1, function(){  
	console.log("Http server listening on port " + port1);
});

https.createServer(options, app).listen(port2, function(){  
	console.log("Https server listening on port " + port2);
});
//

// mysql setup
var connection = mysql.createConnection({
	host     : 'localhost',
	user     : 'root',
	password : 'alvmdpdlQmf',
	port     : 3306,
	database : 'media_proj'
});
//

// answer 등록하기
//var answerList = ["경민호 교수님 정보는 다음과 같습니다.~~~","고욱 교수님 정보는 다음과 같습니다.~~~"];
//var answerUrl = "http://www.mediachatbotproj.tk:8080/retake.php?id=";
//var answerList = answerfile.answerList();
//var answerUrl = answerfile.answerUrl();
//

//session, cookie
app.use(cookieParser("3CCC4ACD-6ED1-4844-9217-82131BDCB239"));
app.use(session({secret:"2C44774A-D649-4D44-9535-46E296EF984F", key: '', store: new MemoryStore(), resave:false, saveUninitialized: true,cookie: { secure: false, httpOnly: false }}));

app.get('/keyboard',function(req,res){
	const menu = {type: 'text'};
	//console.log(req);
	req.session.key='test';
	res.set({'content-type':'application/json','charset':'utf-8'}).send(JSON.stringify(menu));

});


app.post('/message',function(req,res){

	const _obj = { 
		user_key:req.body.user_key,
		type: req.body.type,
		content:req.body.content
	};


	const results = {
		message : {"text":""}
	};

	console.log((_obj.content).includes("pw-"));
	console.log((_obj.content).includes("###"));

	res.setHeader('Access-Control-Allow-Credentials', 'true')
	req.session.key = _obj.user_key;


	/*개인 정보 등록은 웹에서 안내하기! 학번이 등록되어 있다고 가정하고 답을 내림. */

	if((_obj.content).includes("###")){ // 학번 등록

		var len = _obj.length;
		var num = (_obj.content).slice(3,len);

		console.log("student num = ", num);
		console.log("kakao_id num = ", _obj.user_key);

		req.session.message = _obj.content;
		req.session.save();



		connection.query("UPDATE student SET kakao_id = '"+_obj.user_key+"' where st_studentnum = '"+num+"'",
			function(err,result) {

				var numRows = result.affectedRows;

				if(err)
					console.log(err);

				if(numRows == 0){
					results.message.text = "학번 입력값을 다시 확인해주세요. 존재하지 않습니다.";
					res.set({'content-type' :'application/json'}).send(JSON.stringify(results)).end();
				}else{
					console.log("UPDATE studentnum success");
					results.message.text = "학번이 등록되었습니다.";
					res.set({'content-type' :'application/json'}).send(JSON.stringify(results)).end();
				}

			});

	}else if((_obj.content).includes("***")){ // password 등록

		var len = _obj.length;
		var pass = (_obj.content).slice(3,len);


		req.session.message = _obj.content;
		req.session.save();


		connection.query("UPDATE student SET st_password='"+pass+"' where kakao_id = '"+_obj.user_key+"'",
			
			function(err,result) {

				var numRows = result.affectedRows;

				if(err)
					console.log(err);

				if(numRows == 0){
					results.message.text = "학번을 등록해주세요.";
					res.set({'content-type' :'application/json'}).send(JSON.stringify(results)).end();
				}else{
					console.log("UPDATE studentnum success");
					results.message.text = "password가 등록되었습니다. 웹사이트에서 이용가능합니다.";
					res.set({'content-type' :'application/json'}).send(JSON.stringify(results)).end();
				}

			}
			);

	}else if((_obj.content).includes("잘못된 답")){

		console.log(req.sessionID);
		console.log(req.session.message);

		connection.query("UPDATE answerList SET error = 1 where id='"+req.session.message+"'",
			function(err,result) {

				var numRows = result.affectedRows;

				if(err)
					console.log(err);

				if(numRows == 0){
					results.message.text = "관리자에게 문의해주세요.";
					res.set({'content-type' :'application/json'}).send(JSON.stringify(results)).end();
				}else{
					console.log("UPDATE error message success");
					results.message.text = "불편을 드려 죄송합니다. 제공해주신 정보는 차후 서비스 개선에 이용하겠습니다. ";
					res.set({'content-type' :'application/json'}).send(JSON.stringify(results)).end();
				}

	});

	}else{


		var options = {
			mode: 'text',
			pythonPath : 'python3',
			pythonOptions: ['-u'],
			args: _obj.content
		};


	/*	req.session.message = _obj.content;

		console.log("session message");
		console.log(req.session.message);
		req.session.save();

		console.log(req.session);*/


		pythonshell.run('trees.py',options,function(err,answer){

			if(err) console.log(err);

			else {
				console.log(answer[0]);

				var idx = answer[0];

				answerfile.answerList(connection, idx, _obj.user_key,function(str){
					results.message.text = str;
					//results.message.text = answerUrl+_obj.user_key;
					console.log(JSON.stringify(results));


					connection.query("INSERT INTO answerList (kakao_id,content,answer,error) VALUES ('"+_obj.user_key+"','"+_obj.content+"','"+str+"',0)",
						function(err,result) {

							if(err)
								console.log(err);

							console.log("Insert success");
							req.session.message = result.insertId;

							req.session.save(function(err){

								if(err)
									console.log(err)
								else{
									//console.log(req.sessionID);	
									//console.log(req.session);
									req.session.save();

									res.header("Content-Type", "application/json; charset=euc-kr");
									res.send(JSON.stringify(results)).end();
								}

							});

						});

				});
			}

		});
	}

});

function doCall(urlToCall, callback) {
    urllib.request(urlToCall, { wd: 'nodejs' }, function (err, data, response) {                              
        var statusCode = response.statusCode;
        finalData = getResponseJson(statusCode, data.toString());
        return callback(finalData);
    });
}


/*app.get('/saveerr',function(req,res){


	const results = {
		message : {"text":""}
	};

	connection.query("UPDATE answerList SET error = 1 where id='"+req.session.message+"'",
			function(err,result) {

				var numRows = result.affectedRows;

				if(err)
					console.log(err);

				if(numRows == 0){
					results.message.text = "관리자에게 문의해주세요.";
					res.set({'content-type' :'application/json'}).send(JSON.stringify(results)).end();
				}else{
					console.log("UPDATE error message success");
					results.message.text = "불편을 드려 죄송합니다. 제공해주신 정보는 차후 서비스 개선에 이용하겠습니다. ";
					res.set({'content-type' :'application/json'}).send(JSON.stringify(results)).end();
				}

	});

});*/

app.post('/friend',function(req,res){
	console.log(req.body);
	const user_key = req.body.user_key;
	console.log(user_key);

	res.set({'content-type':'application/json'}).send(JSON.stringify({success: true}));
});

app.delete('/friend/:user_key',function(req,res){
 //console.log(req)
 const user_key = req.params.user_key;
 //console.log('X - ${user_key}');


 //학번에 등록된 kakao id 제거
 res.set({'content-type':'application/json'}).send(JSON.stringify({success:true}));
});

app.delete('/chat_room/:user_key',function(req,res){
	const user_key = req.params.user_key;
	console.log('chat_room - ${user_key}');

	res.set({'content-type':'application/json'}).send(JSON.stringify({success:true}));
});



