#!/bin/bash
curl --silent -X POST --header 'Content-Type: application/x-www-form-urlencoded' --header 'Accept: application/json' -d 'email=williamnwogbo@gmail.com&api_key=MovFcE7QLj735fqVwEWc0yYmnD7E5g' 'https://api.cloudways.com/api/v1/oauth/access_token' | cut -f4 -d '"' | sed -e 's/^/export OATH=/' > token

source token

curl --silent -X POST --header 'Content-Type: application/x-www-form-urlencoded' --header 'Accept: application/json' --header 'Authorization: Bearer '$OATH'' -d 'server_id=104017&action=purge' 'https://api.cloudways.com/api/v1/service/varnish'

rm token







