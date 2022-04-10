var http = require('http');
var server = http.createServer();

server.on('request', function(req, res) {
    res.writeHead(200, {'Content-Type': 'application/json'});
    res.write('{"status": "OK"}');
    res.end();
});

server.listen(8080, 'localhost');


