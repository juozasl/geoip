For building local infrastrukture (docker paskage needed localy):

$ ansible-playbook -i "localhost," -c local local.yml

Enter local bash:
$ docker exec -it geoip /bin/bash
$ ./composer.phar update - if needed

Load initial geoipdatabase:
$ docker exec -it geoip /bin/bash
$ ./artisan geoip:download
