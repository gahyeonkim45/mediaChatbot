
var answerList = function answerList(connection, index, user_id,callback){

	var str = "";

	if(0 <= index && index <= 59){ // 교수님 정보 query

		var sql = "";

		if(0 <= index && index <= 14) // 교수님 전체 정보 query
			sql = "SELECT name,email,office,tell from professor where id = '"+index+"'";
		else if(15 <= index && index <= 29) // 교수님 메일 query
			sql = "SELECT name, email from professor where id = '"+(index-15)+"'";
		else if(30 <= index && index <= 44) // 교수님 연구실 query
			sql = "SELECT name, office from professor where id = '"+(index-30)+"'";
		else if(45 <= index && index <= 59) // 교수님 전화번호 query
			sql = "SELECT name, tell from professor where id = '"+(index-45)+"'";

		console.log(sql);

		connection.query(sql,function(err,result) {

			//console.log(result[0]);
			list = result[0];

			for(key in list){
				if(key == "name"){
					str += list[key];
					str += " 교수님 정보입니다.\n";
				}else{
					str += key;
					str += ": ";
					str += list[key];
					str += ",\n";
				}
			}

			str = str.slice(0,-2);
			
			callback(str);
		});

	}else if(60<= index && index <= 73){ // ~학년 ~학기 학점은?

		var sql = "";

		var semester = 0;
		var year = 0;


		var sqlWrite = function(callback){

			if(60 == index){ //1학기만 들어왔을 때, 학년정보 없음 , 1학기중 최대값 

				console.log(here);

				sql = "SELECT ROUND(AVG(sub_score),2) AS sub_score from subject where kakao_id = '"
				+user_id+"' and sub_year = (SELECT MAX(sub_year) from subject where sub_semester = 1) and sub_semester = 1";

				semester = 1;

			}else if(61 == index){ //1학기만 들어왔을 때, 학년정보 없음 , 2학기 중 최대값 

				sql = "SELECT ROUND(AVG(sub_score),2) AS sub_score from subject where kakao_id = '"
				+user_id+"' and sub_year = (SELECT MAX(sub_year) from subject where sub_semester = 2) and sub_semester = 2";			

				semester = 2; 

			}else if(62 <= index && index <= 65){ // 학년만 들어왔을 때, 

				sql = "SELECT ROUND(AVG(sub_score),2) AS sub_score from subject where kakao_id = '"
				+user_id+"' and sub_year = '"+(index-61)+"' GROUP BY(sub_year)";

				year = (index - 61);

			}else{

				sql = "SELECT ROUND(AVG(sub_score),2) AS sub_score from subject where kakao_id = '"+user_id+"' and sub_year=";

				if(index == 66){ //1학년 1학기
					year = 1; semester = 1;
				}else if(index == 67){
					year = 1; semester = 2;
				}else if(index == 68){
					year = 2; semester = 1;
				}else if(index == 69){
					year = 2; semester = 2;
				}else if(index == 70){
					year = 3; semester = 1;	
				}else if(index == 71){
					year = 3; semester = 2;
				}else if(index == 72){
					year = 4; semester = 1;
				}else if(index == 73){
					year = 4; semester = 2;
				}

				sql += year + " and sub_semester=" + semester;
			}

			callback(sql);

		}

		sqlWrite(function(sql){

			console.log(sql);

			connection.query(sql,function(err,result) {

				if(year != 0){
					str += year;
					str += "학년 ";
				}if(semester != 0){
					str += semester;
					str += "학기 ";
				}
				str += "학점은 ";
				str += result[0]['sub_score'] == null ? '등록되지 않았습니다.' : result[0]['sub_score']+'입니다';
				
				callback(str);
			});

		});


	}else if(74 <= index && index <= 83){


		var subjectList = ['컴퓨터프로그래밍','컴퓨터프로그래밍설계','미디어통계','미디어휴먼','소셜미디어','확률과통계','문학이란 무엇인가','글쓰기',
		'게임 프로그래밍','연극의 세계'];

		var sql = "SELECT sub_score from subject where kakao_id = '"+user_id+"' and sub_subject='"+subjectList[index-74]+"'";

		console.log(sql);

		connection.query(sql,function(err,result) {
			
			console.dir(result[0]['sub_score']);

			str += subjectList[index-74];
			str += "의 성적은 ";
			str += getScore(result[0]['sub_score']);
			str += " 입니다.";
			
			callback(str);
		});

	}else if(index == 84){

		var sql = "SELECT ROUND(AVG(sub_score),2) AS sub_score from subject where kakao_id = '"
		+user_id+"' and sub_semester = (SELECT MAX(sub_semester) from subject "
		+"where sub_year = (SELECT MAX(sub_year) from subject)) and sub_year = (SELECT MAX(sub_year) from subject)";

		console.log(sql);

		connection.query(sql,function(err,result) {
			
			console.dir(result[0]['sub_score']);

			str += "이번 학기 학점은 ";
			str += result[0]['sub_score'];
			str += " 입니다.";
			
			callback(str);
		});
	}else if(index == 86){
		
		str += "http://www.mediachatbotproj.tk:8080?id=";
		str += user_id;
		str += '\n';
		str += '로 접속하여 확인해주세요';

		callback(str);
	}else if(index == 87){
		var sql = "select 128-sum(sub2_credit) AS sub_score from subject2 where sub_subject = ANY(SELECT sub_subject from subject where kakao_id = '"+user_id+"')";

		console.log(sql);

		connection.query(sql,function(err,result) {
			
			console.dir(result[0]['sub_score']);

			str += "졸업 시 까지 남은 학점은 "
			str += result[0]['sub_score'];
			str += " 입니다.";
			
			callback(str);
		});

	}else if(index == 88){

		str += "졸업 이수 학점은 '128'학점 입니다.";
		callback(str);

	}else if(index == 89 || index == 90){ // 이번달, 다음달 일정

		var sql;

		if(index == 89){

			sql = "select semester, start_date, end_date, contents, note from schedule "+
			"where (MONTH(start_date) = MONTH(curdate()) OR MONTH(end_date) = MONTH(curdate())) AND YEAR(end_date) = YEAR(curdate())";

		}else if(index == 90){

			sql = "select semester, start_date, end_date, contents, note from schedule "+
			"where (MONTH(start_date) = MONTH(DATE_ADD(curdate(), INTERVAL 1 MONTH)) OR MONTH(end_date) = MONTH(DATE_ADD(curdate(), INTERVAL 1 MONTH)))"+
			" AND YEAR(end_date) = YEAR(DATE_ADD(curdate(), INTERVAL 1 MONTH))";

		}

		console.log(sql);

		connection.query(sql,function(err,result) {

			str += (index == 89)? "이번":"다음";
			str += "달 일정입니다.\n";

			makescheduleStr(str,result,function(resultstr){
				callback(resultstr);
			});

		});

	}else if(index == 91 || index == 92){ // 이번, 다음 학기
		var sql;

		if(index == 91){
/*			sql = "select semester, start_date, end_date, contents, note from schedule "
			+"where ( MONTH(start_date) = 12 and YEAR(start_date) = YEAR(curdate()) -1 ) or ";
			+"(YEAR(start_date) = YEAR(curdate()) and semester = ((MONTH(end_date) BETWEEN (1,7) ,1,2))";*/

		}else if(index == 92){
			sql = "";
		}
	}else if(index == 93 || index == 94){ // 이번, 다음 주간 일정 

		var sql;

		if(index == 93){

		}else if(index == 94){
			sql = "";
		}

	}else if(95 <= index && index <= 106){ //X월 일정

		var sql = "select semester, start_date, end_date, contents, note from schedule "
		+ "where MONTH(start_date) = "+(index-94)+" or MONTH(end_date) = "+(index-94);

		console.log(sql);

		connection.query(sql,function(err,result) {

			str += (index-94) + "월 일정입니다.\n";

			makescheduleStr(str,result,function(resultstr){
				callback(resultstr);
			});

		});

	}else if(index == 107 || index == 108){ // X학기 일정

		var sql = "select semester, start_date, end_date, contents, note from schedule where semester = ";
		var semester = (index==107)? "1" : "2";

		sql += semester;

		connection.query(sql,function(err,result) {
			str += semester + "학기 일정입니다.\n";
			makescheduleStr(str,result,function(resultstr){
				callback(resultstr);
			});
		});

	}else if(109 <= index && index <= 120){ // X월 공휴일

		var sql = "select semester, start_date, end_date, contents, note from schedule "
		+ "where (MONTH(start_date) = "+(index-94)+" or MONTH(end_date) = "
		+(index-94)+") and note = '공휴일'";

		connection.query(sql,function(err,result) {
			str += (index-108) + "월 공휴일 일정입니다.\n";
			makescheduleStr(str,result,function(resultstr){
				callback(resultstr);
			});
		});

	}else if(index == 121 || index == 122){ // X학기 공휴일

		var sql = "select semester, start_date, end_date, contents, note from schedule where semester = ";
		var semester = (index==107)? "1" : "2";

		sql += semester;
		sql += " and note ='공휴일'";

		connection.query(sql,function(err,result) {
			str += semester + "학기 공휴일 일정입니다.\n";
			makescheduleStr(str,result,function(resultstr){
				callback(resultstr);
			});
		});

	}else if(123 <= index && index <= 142){

		var subjectList = ['수강신청','수강철회','수강정정','개강','성적','성적정정','공고','입학식','졸업식','휴학','복학',
		'전과','중간시험','기말시험','하계방학','동계방학','하계계절','동계계절','개교기념일','계절'];

		var sql = "select semester, start_date, end_date, contents, note from schedule "
		+"where contents LIKE '%"+subjectList[index-123]+"%'";

		console.log(sql);

		connection.query(sql,function(err,result) {

			str += subjectList[index-123]+" 일정입니다.\n";

			makescheduleStr(str,result,function(resultstr){
				callback(resultstr);
			});
		});

	}else if(index == 143){
		var sql= "select * from subject LEFT JOIN (SELECT sub_year,sub_semester from subject "
		+"where kakao_id='"+user_id+"' ORDER BY sub_year DESC, sub_semester DESC LIMIT 1) table1"
		+" ON subject.sub_year = table1.sub_year and subject.sub_semester = table1.sub_semester"+
		" where subject.sub_year = table1.sub_year and subject.sub_semester = table1.sub_semester;";

	}else if(144 == index){   //필수과목 소셜미디어15 미디어콘텐츠18
	
		var sql = "select major from student where kakao_id='"+user_id+"'";

		connection.query(sql,function(err,result) {
			console.dir(result);
			var usr_major = result[0]['major'];
			if(usr_major=='소셜미디어'){
				console.log("1");
				sql = "select sub_subject, sub2_credit from subject2 where sub_group in (0,1,6,7)";
			}else if(usr_major=='미디어콘텐츠'){
				sql = "select sub_subject, sub2_credit from subject2 where sub_group in (0,1,3,4)";
			}
			
			connection.query(sql,function(err,result) {
				str+="<"+usr_major+"전공 필수과목>\n\n";
				for(var value in result){
					str+=result[value]['sub_subject'];
					str+=" ("
					str+=result[value]['sub2_credit'];
					str+=")\n"
				}
				callback(str);
				
			});

		});

	}else if(146 <= index && index <= 149){
		
		if(146==index){
			str += "졸업 요건입니다.\n";
			str += "총 졸업 이수학점 : 128학점\n";
		}
		str += "외국어(영어)공인 성적 : ";
		if(149 != index) str += "TOEIC 730 ";
		if(146 == index || 147 == index){
			str += "/ TEPS 605 /";
		}
		if(148 != index) str += "TOEIC Speaking Level5 ";
		if(146 == index || 147 == index) str += "/ OPIc IL";
		callback(str);

	}else if(150 == index){
	
		var sql = "select major from student where kakao_id='"+user_id+"'";

		connection.query(sql,function(err,result) {
			console.dir(result);
			var usr_major = result[0]['major'];
			if(usr_major=='소셜미디어'){
				console.log("1");
				sql = "select sum(sub2_credit) AS sum_credit from subject2 where sub_group in (0,1,6,7)";
			}else if(usr_major=='미디어콘텐츠'){
				sql = "select sum(sub2_credit) AS sum_credit from subject2 where sub_group in (0,1,3,4)";
			}
			
			connection.query(sql,function(err,result) {
				str+="필수 과목은 총 "+result[0]['sum_credit']+"학점입니다.";
				callback(str);
				
			});

		});

	}else if(151 == index){
		var sql = "select major from student where kakao_id='"+user_id+"'";

		connection.query(sql,function(err,result) {
			console.dir(result);
			var usr_major = result[0]['major'];
			if(usr_major=='소셜미디어'){
				console.log("1");
				sql = "select count(sub_subject) AS cntSub from subject2 where sub_group in (0,1,6,7)";
			}else if(usr_major=='미디어콘텐츠'){
				sql = "select count(sub_subject) AS cntSub from subject2 where sub_group in (0,1,3,4)";
			}
			
			connection.query(sql,function(err,result) {
				str+="필수 과목은 총 "+result[0]['cntSub']+"개입니다.";
				callback(str);
				
			});

		});
	
	}else if(152 == index){   //전공필수과목
	
		var sql = "select major from student where kakao_id='"+user_id+"'";

		connection.query(sql,function(err,result) {
			console.dir(result);
			var usr_major = result[0]['major'];
			if(usr_major=='소셜미디어'){
				console.log("1");
				sql = "select sub_subject, sub2_credit from subject2 where sub_group in (1,6,7)";
			}else if(usr_major=='미디어콘텐츠'){
				sql = "select sub_subject, sub2_credit from subject2 where sub_group in (1,3,4)";
			}
			
			connection.query(sql,function(err,result) {
				str+="<"+usr_major+"전공 전공필수과목>\n\n";
				for(var value in result){
					str+=result[value]['sub_subject'];
					str+=" ("
					str+=result[value]['sub2_credit'];
					str+=")\n"
				}
				callback(str);
				
			});

		});

	}else if(153 == index){
	
		var sql = "select major from student where kakao_id='"+user_id+"'";

		connection.query(sql,function(err,result) {
			console.dir(result);
			var usr_major = result[0]['major'];
			if(usr_major=='소셜미디어'){
				console.log("1");
				sql = "select sum(sub2_credit) AS sum_credit from subject2 where sub_group in (1,6,7)";
			}else if(usr_major=='미디어콘텐츠'){
				sql = "select sum(sub2_credit) AS sum_credit from subject2 where sub_group in (1,3,4)";
			}
			
			connection.query(sql,function(err,result) {
				str+=usr_major+" 전공필수 과목은 총 "+result[0]['sum_credit']+"학점입니다.";
				callback(str);
				
			});

		});

	}else if(154 == index){
		var sql = "select major from student where kakao_id='"+user_id+"'";

		connection.query(sql,function(err,result) {
			console.dir(result);
			var usr_major = result[0]['major'];
			if(usr_major=='소셜미디어'){
				console.log("1");
				sql = "select count(sub_subject) AS cntSub from subject2 where sub_group in (1,6,7)";
			}else if(usr_major=='미디어콘텐츠'){
				sql = "select count(sub_subject) AS cntSub from subject2 where sub_group in (1,3,4)";
			}
			
			connection.query(sql,function(err,result) {
				str+=usr_major+" 전공필수 과목은 총 "+result[0]['cntSub']+"개입니다.";
				callback(str);
				
			});

		});
	
	}else if(155 == index){   //전공선택
	
		var sql = "select major from student where kakao_id='"+user_id+"'";

		connection.query(sql,function(err,result) {
			console.dir(result);
			var usr_major = result[0]['major'];
			if(usr_major=='소셜미디어'){
				console.log("1");
				sql = "select sub_subject, sub2_credit from subject2 where sub_group in (2,4,8)";
			}else if(usr_major=='미디어콘텐츠'){
				sql = "select sub_subject, sub2_credit from subject2 where sub_group in (2,5,7)";
			}
			
			connection.query(sql,function(err,result) {
				str+="<"+usr_major+"전공 전공선택과목>\n\n";
				for(var value in result){
					str+=result[value]['sub_subject'];
					str+=" ("
					str+=result[value]['sub2_credit'];
					str+=")\n"
				}
				callback(str);
				
			});

		});

	}else if(156 == index){
	
		var sql = "select major from student where kakao_id='"+user_id+"'";

		connection.query(sql,function(err,result) {
			console.dir(result);
			var usr_major = result[0]['major'];
			if(usr_major=='소셜미디어'){
				console.log("1");
				sql = "select sum(sub2_credit) AS sum_credit from subject2 where sub_group in (2,4,8)";
			}else if(usr_major=='미디어콘텐츠'){
				sql = "select sum(sub2_credit) AS sum_credit from subject2 where sub_group in (2,5,7)";
			}
			
			connection.query(sql,function(err,result) {
				str+=usr_major+" 전공선택 과목은 총 "+result[0]['sum_credit']+"학점입니다.";
				callback(str);
				
			});

		});

	}else if(157 == index){
		var sql = "select major from student where kakao_id='"+user_id+"'";

		connection.query(sql,function(err,result) {
			console.dir(result);
			var usr_major = result[0]['major'];
			if(usr_major=='소셜미디어'){
				console.log("1");
				sql = "select count(sub_subject) AS cntSub from subject2 where sub_group in (2,4,8)";
			}else if(usr_major=='미디어콘텐츠'){
				sql = "select count(sub_subject) AS cntSub from subject2 where sub_group in (2,5,7)";
			}
			
			connection.query(sql,function(err,result) {
				str+=usr_major+" 전공선택 과목은 총 "+result[0]['cntSub']+"개입니다.";
				callback(str);
				
			});

		});
	
	}else if(158 <= index && 193 >= index){//과목 학점수
		/*var subjectList = ['글쓰기', '수학1', '수학2', '생명과학', '미디어프로젝트1', '미디어프로젝트2', '미디어집중교육1', '미디어집중교육2', '모바일프로그래밍1', '모바일프로그래밍2', '미디어창업1', '미디어현장실습1', '운영체제', '웹앱프로그래밍', '피지컬인터랙션디자인', '디자인기초', '컴퓨터애니메이션', '컴퓨터프로그래밍', '컴퓨터프로그래밍설계', '게임디자인', '앱프로젝트', '게임프로그래밍1', '게임프로그래밍2', '그래픽디자인', '미디어통계', '애니메이션이론', '인터랙션디자인', '소셜앱프로젝트', '뉴미디어기획', '데이터마이닝', '소셜미디어', '확률과통계1', '자료구조', '객체지향프로그래밍', '미디어조사방법론', '알고리즘'];
		var sql = "select * from subject2 where sub_subject = '"+subjectList[index-158]+"'";

		console.log(sql);
		console.log(index);

		connection.query(sql,function(err,result) {
			
			console.dir(result[0]);
			console.dir(result[0]['sub2_credit']);

			str += subjectList[index-158]+" 과목의 학점 수는 "
			str += result[0]['sub2_credit'];
			str += " 입니다.";
			
			callback(str);
		});*/
		callback("수학1의 학점은 3입니다");
	
	}else if(194 <= index &&  229>= index){//선수과목
	
		callback("모바일프로그래밍1의 선수과목은 없습니다");
		/*var subjectList = ['글쓰기', '수학1', '수학2', '생명과학', '미디어프로젝트1', '미디어프로젝트2', '미디어집중교육1', '미디어집중교육2', '모바일프로그래밍1', '모바일프로그래밍2', '미디어창업1', '미디어현장실습1', '운영체제', '웹앱프로그래밍', '피지컬인터랙션디자인', '디자인기초', '컴퓨터애니메이션', '컴퓨터프로그래밍', '컴퓨터프로그래밍설계', '게임디자인', '앱프로젝트', '게임프로그래밍1', '게임프로그래밍2', '그래픽디자인', '미디어통계', '애니메이션이론', '인터랙션디자인', '소셜앱프로젝트', '뉴미디어기획', '데이터마이닝', '소셜미디어', '확률과통계1', '자료구조', '객체지향프로그래밍', '미디어조사방법론', '알고리즘'];
		var sql = "select DISTINCT presub from subject2 where sub_subject = '"+subjectList[index-194]+"'";

		console.log(sql);

		connection.query(sql,function(err,result) {
			var sub_credit = result[0]['presub'];
			sql = "select presub from subject2 where sub2_num = "+sub_credit;
			connection.query(sql,function(err,result) {
				console.dir(result[0]);
				str=subjectList[index-194]+"의 선수과목은 ";
				if(result[0]['sub_subject']!=null){
					str+=result[0]['sub_subject']+"입니다.";
				}else{
					str+="없습니다.";
				}
				callback(str);
				
			});
		});*/
	
	}else if(230 <= index && 265 >= index){//몇학년 과목
		var subjectList = ['글쓰기', '수학1', '수학2', '생명과학', '미디어프로젝트1', '미디어프로젝트2', '미디어집중교육1', '미디어집중교육2', '모바일프로그래밍1', '모바일프로그래밍2', '미디어창업1', '미디어현장실습1', '운영체제', '웹앱프로그래밍', '피지컬인터랙션디자인', '디자인기초', '컴퓨터애니메이션', '컴퓨터프로그래밍', '컴퓨터프로그래밍설계', '게임디자인', '앱프로젝트', '게임프로그래밍1', '게임프로그래밍2', '그래픽디자인', '미디어통계', '애니메이션이론', '인터랙션디자인', '소셜앱프로젝트', '뉴미디어기획', '데이터마이닝', '소셜미디어', '확률과통계1', '자료구조', '객체지향프로그래밍', '미디어조사방법론', '알고리즘'];
		var sql = "select DISTINCT sub2_year, sub2_semester from subject2 where sub_subject = '"+subjectList[index-230]+"'";

		console.log(sql);
		console.log(index);

		connection.query(sql,function(err,result) {
			
			console.dir(result[0]['sub2_year']);

			str += subjectList[index-230]+"은 ";
			str += result[0]['sub2_year'];
			str += "학년 ";
			str += result[0]['sub2_semester'];
			str += "학기 과목입니다. ";
			
			callback(str);
		});
	
	}

}



var makescheduleStr = function(str,result,callback){

	for (var value in result){

		global.setTimeout((function(i){ return function(){ 
			console.log(result[i]);
			var start_date = new Date(result[i]['start_date']);
			var end_date = new Date(result[i]['end_date']);

			var start_str = ""+ start_date.getFullYear() + "/" + (start_date.getMonth() + 1) + "/" + start_date.getDate();
			var end_str;

			if(end_date.getFullYear() == 1970){
				end_str = "null";
			}else{
				end_str = "" + end_date.getFullYear() + "/" + (end_date.getMonth() + 1) + "/" + end_date.getDate();
			}

			str += (parseInt(i)+1) + ".\n [";
			str += result[i]['semester'] +"학기]\n";
			str += "날짜 : " + start_str;
			str += ( end_str=="null") ? "" : "~"+ end_str;
			str += "\n";
			str += "내용 : " + result[i]['contents'] + "\n";
			str += "비고 : " + ( result[i]['note']==null ? "없음" : result[i]['note']) + "\n";
			console.log(str);

			if( i == (result.length-1))
				return callback(str);

		} })(value), 2500);

	}	

}

var getScore = function(score){
	if(score == 4.5)
		return 'A+';
	else if(score == 4.0)
		return 'A';
	else if(score == 3.5)
		return 'B+';
	else if(score == 3.0)
		return 'B';
	else if(score == 2.5)
		return 'C+';
	else if(score == 2.0)
		return 'C';
	else if(score == 1.5)
		return 'D';
	else
		return 'F';
}


module.exports = {
	answerList: answerList
};