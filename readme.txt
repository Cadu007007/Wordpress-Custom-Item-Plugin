
REST API DOCUMENTATION

1 - GET ITEM

ENDPOINT: {base_url}/wp-json/items/get
TYPE: GET
PARAMETERS: item_id
RESPONSE: item
SUCCESS RESPONSE CODE 200

2 - CREATE ITEM

ENDPOINT: {base_url}/wp-json/items/create
TYPE: POST
PARAMETERS: title, description
RESPONSE: message, item
SUCCESS RESPONSE CODE 200

3 - EDIT ITEM

ENDPOINT: {base_url}/wp-json/items/edit
TYPE: POST
PARAMETERS: item_id, title, description
RESPONSE: message, item
SUCCESS RESPONSE CODE 200

4 - DELETE ITEM

ENDPOINT: {base_url}/wp-json/items/delete
TYPE: GET
PARAMETERS: item_id
RESPONSE: message
SUCCESS RESPONSE CODE 200

5 - GET All ITEMS

ENDPOINT: {base_url}/wp-json/items/get-all
TYPE: GET
PARAMETERS: page
RESPONSE: items, page
SUCCESS RESPONSE CODE 200