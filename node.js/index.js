let secret_key='123456789@#';
console.log('Secret Key:',secret_key);

const WSServer = require('./src/mcpews');
const BuildSession = require('./src/session');
let wss = new WSServer(23333);
let http = require('http');
let url = require('url');
let util = require('util');
let http_port = 23334;
let querystring = require('querystring');
let fs = require("fs");

let requests = {};

wss.on('client', (session, request) => {

	console.log(request.connection.remoteAddress + ' connected!');

	BuildSession.createAndBind(session);
	session.sendText('§b§lPlugMCApi§r by §e§o§lHanmo§r §l& §bLMWS§r by §e§o§lCAIMEO §r§l& §bAPI§r by §e§o§lUUZ§r.');
	session.on('onMessage',(msg, player)=>{
		console.log(`[${player}]: `, msg);
	});

	session.on('onError',(e)=>{
		console.log('onError: ', e);
	});
	
	session.on("onJSON", function (json) {
        console.log(json);

        if (typeof json !== "string"){
            return;
        }

        let json_obj = JSON.parse(json);

        // 判断类型
        if (json_obj.header === undefined){
            return;
        }

        // this req id
        let json_reqid = json_obj.header.requestId;

        // req id verification
        if (requests[json_reqid] === undefined){
            return;
        }

        // get body
        let res_body = json_obj.body;

        // return
        let ret = {
            status: true,
            code: 200,
            message: "200 OK!",
            payload: res_body
        };

        requests[json_reqid].res.write(JSON.stringify(ret));
        requests[json_reqid].res.end();

        console.log(JSON.stringify(ret));

        return;
    });

    http.createServer(function(req, res){

        // Header
        res.writeHead(200, {'Content-Type': 'text/json; charset=utf-8'});

        let res_status='true';
        let getData = url.parse(req.url, true).query;

        let post = '';

        req.on('data', function(chunk){
            post += chunk;
        });

        req.on('end', function(){

            // return message constructor
            let message = {};

            post = querystring.parse(post);

            console.log('---Receive API Request');
            console.log('Type:',getData.type);
            console.log('Body:',post);

            if (post.key !== secret_key) {
                message.status = false;
                message.code = 401;
                message.message = "Input key does NOT match the server key!";

                res.write(JSON.stringify(message));
                res.end();

                return;
            }

            if (getData.type === 'cmd'){

                let req_uuid = session.sendCommand(post.cmd);
                console.log("requestID: " + req_uuid);

                requests[req_uuid] = {
                    res: res,
                    req_id: req_uuid
                };

            }else if(getData.type==='fast_send'){
            	session.sendCommand(post.cmd);
            	res.write('true');
                res.end();
            }else  if(getData.type==='batch'){
                if(post.cmd1!=='null'){
                    session.sendCommand(post.cmd1);
                }
                if(post.cmd2!=='null'){
                    session.sendCommand(post.cmd2);
                }
                if(post.cmd3!=='null'){
                    session.sendCommand(post.cmd3);
                }
                if(post.cmd4!=='null'){
                    session.sendCommand(post.cmd4);
                }
                res.write('true');
                res.end();

            }else{
                res.write('false');
                res.end();
            }
            
        });
    }).listen(http_port);
});
