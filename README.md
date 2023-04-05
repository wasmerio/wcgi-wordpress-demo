# WCGI WordPress Demo

This demo contains a WordPress website that can be run as a WCGI program by
Wasmer.

## Getting Started

To run this demo, you will need to install [the Wasmer toolchain][install].

```console
$ curl https://get.wasmer.io -sSfL | sh -s "v3.2.0-beta.2"
```

(version `v3.2.0-beta.2` or more recent is required).

You can use `wasmer run-unstable` to run the website locally.

```console
$ wasmer run-unstable . --mapdir=/db:db
INFO run: wasmer_wasix::runners::wcgi::runner: Starting the server address=127.0.0.1:8000 command_name="php"
```

# Hacks / Adaptations

This codebase does the following hacks to Wordpress to have it full running:

* Replace `exit;` or `die;` to `exit(0);` or `die(0);`: they are equivalent, but they don't work without the arguments on the `php-cgi` WASI counterpart
* Call `fwrite(STDOUT, '');` before `exit` as any header sent before an exit will be skipped if no body is being written

Replaced `wp-includes/class-wp-http.php`, `wp-includes/class-wp-http-streams.php`, `wp-includes/class-wp-http-curl.php` to return `WP_Error`;

```php
	public function request( $url, $args = array() ) {
		return new WP_Error( 'http_request_failed', __( "The HTTP request to $url failed." ) );
  }
```

Replaced `_maybe_update_plugins` in `wp-includes/update.php`:

```php
function _maybe_update_plugins() {
	return [
		"last_checked" => 1680680646,
	];
```

## License

This project is licensed under either of

- Apache License, Version 2.0, ([LICENSE-APACHE](./LICENSE-APACHE.md) or
  <http://www.apache.org/licenses/LICENSE-2.0>)
- MIT license ([LICENSE-MIT](./LICENSE-MIT.md) or
   <http://opensource.org/licenses/MIT>)

at your option.

### Contribution

Unless you explicitly state otherwise, any contribution intentionally
submitted for inclusion in the work by you, as defined in the Apache-2.0
license, shall be dual licensed as above, without any additional terms or
conditions.

[install]: https://docs.wasmer.io/ecosystem/wasmer/getting-started
