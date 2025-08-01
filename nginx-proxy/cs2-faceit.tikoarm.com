server {
    listen 80;
    server_name cs2-faceit.tikoarm.com;

    location / {
        proxy_pass http://localhost:8890/;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }
}
