# WP REST PHP Library

Use this library to interact with the WordPress.com REST or WP JSON REST APIs in your PHP projects.

# Usage Examples

## WP.com

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
$client->request_access_token( $auth_code, 'http://redirect.example.com' );
```

### Get site details

Use the WPCOM_REST_Object_Site object to interact with a site:

```
$client = new WPCOM_REST_Client;
$site = WPCOM_REST_Object_Site::withId( 'foo.wordpress.com', $client );
$site_details = $site->get();
echo $site_details->name;

$recent_posts = $site->get_posts();
echo 'There are ' . count( $recent_posts->posts ) . ' recent posts';
```

### Interacting with posts

Use the WPCOM_REST_Object_Post object to interact with posts:

```
$client = new WPCOM_REST_Client;
$post = WPCOM_REST_Object_Post::withId( 1, 'foo.wordpress.com', $client );
$post_data = $post->get()
echo "Post Title: " . $post_data->title;

$post->update_post( array( 'title' => 'New Post Title' ) );

$post->delete_post();
```

Create new posts using the `asNew` factory method:

```
$client = new WPCOM_REST_Client;
// [snip: add auth data to client] 
$post = WPCOM_REST_Object_Post::asNew( array( 'title' => 'New Post' ), 'foo.wordpress.com', $client );
$post_data = $post->get();
```

# Alternate Transports

The library supports pluggable transports. If you'd prefer to use the WordPress' HTTP for example, you can pass that into your client:

```
$client = new WPCOM_REST_Client;
$client->set_api_transport( new WPCOM_REST_Transport_WP_HTTP_API );
```
