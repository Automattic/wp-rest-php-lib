# WP REST PHP Library

Use this library to interact with the WordPress.com REST or WP JSON REST APIs in your PHP projects.

# Usage Examples

## WP.com

Use the `WPCOM_REST_Client` and various `REST_Object` classes to interact with the API.

### Authentication

```
$client = new WPCOM_REST_Client;
// Set key and secret
$client->set_auth_key( 'your-app-key', 'your-app-secret' );

// If you have a token, set it:
$client->set_auth_token( 'your-token' );

// If you don't have a token, you can make the user to the OAuth dance using the helper methods:
$client->get_blog_auth_url( 'foo.wordpress.com', 'http://redirect.example.com' );
// Request the auth token once you get the auth code after the user approves the app
try {
	$client->request_access_token( $auth_code, 'http://redirect.example.com' );
} catch ( WP_REST_Exception $e ) { /* error handling */ }
```

### Get site details

Use the `WPCOM_REST_Object_Site` object to interact with a site:

```
$client = new WPCOM_REST_Client;

try {
	$site = WPCOM_REST_Object_Site::initWithId( 'foo.wordpress.com', $client );
	$site_details = $site->get();
	echo $site_details->name;

	$recent_posts = $site->get_posts();
	echo 'There are ' . count( $recent_posts->posts ) . ' recent posts';
} catch ( WP_REST_Exception $e ) { /* error handling */ }
```

### Interacting with posts

Use the `WPCOM_REST_Object_Post` object to interact with posts:

```
$client = new WPCOM_REST_Client;
try {
	$post = WPCOM_REST_Object_Post::initWithId( 1, 'foo.wordpress.com', $client );
	$post_data = $post->get()
	echo "Post Title: " . $post_data->title;

	$post->update_post( array( 'title' => 'New Post Title' ) );

	$post->delete_post();
} catch ( WP_REST_Exception $e ) { /* error handling */ }
```

Create new posts using the `initAsNew` factory method:

```
$client = new WPCOM_REST_Client;

// [snip: add auth data to client] 

try {
	$post = WPCOM_REST_Object_Post::initAsNew( array( 'title' => 'New Post' ), 'foo.wordpress.com', $client );
	$post_data = $post->get();
} catch ( WP_REST_Exception $e ) { /* error handling */ }
```

## WP-API

Use the `WPAPI_REST_Client` and various `REST_Object` classes to interact with the API.

Make sure that the [WP-API plugin](https://github.com/WP-API/WP-API) is installed.

### Authentication

Currently, only Basic Auth is supported (requires [this plugin](https://github.com/WP-API/Basic-Auth)). OAuth coming soon.

For basic auth, use the `WPAPI_REST_Basic_Auth_Client` and pass the username and password for your user when instantiating the object.

```
$wp_api_client = new WPAPI_REST_Basic_Auth_Client( 'http://wp.example.com', 'username', 'password' );
```

### Interacting with individual posts

Use the `WPAPI_REST_Object_Post` class:

```
try {
	$new_post = WPAPI_REST_Object_Post::initAsNew( $post_data, $wp_api_client );
	$new_post_data = $new_post->get();
	echo 'Post ID: ' . $new_post_data->ID;
	
	$current_post = WPAPI_REST_Object_Post::initWithId( 1, $wp_api_client );
	$current_post_data = $current_post->get();
	echo 'Post Title:' . $current_post_data->ID;

	$current_post->update( array( /* ... */ ) );

	$current_post->delete();
} catch ( WP_REST_Exception $e ) { /* error handling */ }
```

### Post Collections

Use the `WPAPI_REST_Object_Posts` class:

```
try {
	$posts = WPAPI_REST_Object_Posts::init();
	$posts_list = $posts->get();
	echo "Number of posts found: " . count( $posts_list );
} catch ( WP_REST_Exception $e ) { /* error handling */ }
```

# Alternate Transports

The library supports pluggable transports. If you'd prefer to use the WordPress' HTTP for example, you can pass that into your client:

```
require_once( 'src/class-wp-rest-transport-wp-http-api.php' );
$client = new WPCOM_REST_Client;
$client->set_api_transport( new WP_REST_Transport_WP_HTTP_API );
```

# Errors / Failures

On failure, the library will throw exceptions, so make sure you're catching them properly:

```
try {
	$post = WPCOM_REST_Object_Post::initAsNew( array( 'title' => 'New Post' ), 'foo.wordpress.com', $client );	
} catch ( WP_REST_Exception $e ) {
	// log the error
	// handle the error case
}
```