var secret_key='123456789@#';
console.log('Secret Key:',secret_key);

const WSServer = require('./src/mcpews');
const BuildSession = require('./src/session');
var wss = new WSServer(23333);
var http = require('http');
var url = require('url');
var util = require('util');
var http_port = 23334;
var querystring = require('querystring');
var fs = require("fs");

wss.on('client', (session, request) => {
	console.log(request.connection.remoteAddress + ' connected!');
	
	BuildSession.createAndBind(session);

	session.sendText('§b||§lPlugMCApi§r by §e§o§lHanmo§r §l& §bLMWS§r by §e§o§lCAIMEO§r.');

	session.on('onMessage',(msg, player)=>{
		//当服务器接收到玩家发出信息
		console.log(`[${player}]: `, msg);
	});

	session.on('onCommand',(json)=>{
		//当玩家发送了一个Chat命令
		console.log('CommandOptions: ', json);
	});

	session.on('onJSON',(json)=>{
		//JSON
		console.log('onJSON: ', json);
    	fs.writeFile('output.log', json, function(err) {
            if (err) {
                return console.error(err);
            }
        });
	});

	session.on('onError',(e)=>{
		//发生错误时
		console.log('onError: ', e);
	});

	//从http服务器获取命令
    http.createServer(function(req, res){
        res.writeHead(200, {'Content-Type': 'text/plain; charset=utf-8'});
        var getdata = url.parse(req.url, true).query;
        var post = '';
        req.on('data', function(chunk){
            post += chunk;
        });
        req.on('end', function(){
            post = querystring.parse(post);
            console.log('---Receive API Request');
            console.log('Type:',getdata.type);
            console.log('Body:',post);
            if(post.key==secret_key){
                if(getdata.type=='cmd'){
                    //发送指令
                    session.sendCommand(post.cmd);
                    res.write('true');
                    res.end();
                }else if(getdata.type=='getlog'){
                    //获取上次执行结果
                    var data = fs.readFileSync('output.log');
                    res.write(data);
                    res.end();
                }else{
                    //不存在的类型
                    res.write('false');
                    res.end();
                }
            }else{
                //密钥错误
                res.write('false');
                res.end();
            }
        });
    }).listen(http_port);
});