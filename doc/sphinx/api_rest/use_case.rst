########
Use Case
########

Let's put a little use case as an example: A platform admin wants to obtain the companies of one specific brand (companies are exposed on Brand API only). The operation would be:

.. rubric:: On platform API (https://your-domain/api/platform)

#. Login (request a token) as god admin on /admin_login

#. Search target brand on /brands

#. Get it's domain on /web_portals

#. Get a valid brand administrator on /administrators

.. rubric:: On Brand API (https://brand-domain/api/brand)

#. Impersonate as a brand admin on Auth> /token/exchange (requires a god token and a brand administrator user name obtained in 1-d)

#. Request brand companies using the endpoint /companies

.. rubic:: Practical example with swager-codegen Python Brand Api

#. Download last java stable package swagerver compatible:
    - Java 8+
    - ``wget https://repo1.maven.org/maven2/io/swagger/swagger-codegen-cli/2.4.12/swagger-codegen-cli-2.4.12.jar``
    
#. Generate python package: ``java -jar swagger-codegen-cli-2.4.12.jar generate -i https://YOUR.IVOZ.DOMAIN/api/brand/swagger.json -l python``

#. Code for retrive list of all companies

.. code-block:: Python

  import swagger_client
  from swagger_client.rest import ApiException
  import json

  # Configure API key authorization: bearer
  configuration = swagger_client.Configuration()
  configuration.host = 'https://YOUR.HOST.IVOZ.sip/api/brand'
  configuration.api_key['Authorization'] = 'YOUR_API_KEY'
  configuration.api_key_prefix['Authorization'] = 'Bearer'

  # create an instance of the API class
  api_instance = swagger_client.AuthApi(swagger_client.ApiClient(configuration))
  username = 'USERNAME OPERADOR BRAND' # str |
  password = 'PASSWORD OPERADOR BRAND' # str |

  try:
      # Retrieve JWT token
      api_instance.post_admin_authenticate(username, password)
      configuration.api_key['Authorization'] = json.loads(api_instance.api_client.last_response.data)['token']
  except ApiException as e:
      print("Exception when calling AuthApi->post_admin_authenticate: %s\n" % e)

  list_of_companies = swagger_client.ProviderApi(swagger_client.ApiClient(configuration))
