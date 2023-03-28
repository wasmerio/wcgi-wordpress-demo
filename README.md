# WCGI Template for PHP

This is a template project for creating a WCGI-compatible package that can be
published to WAPM.

## Getting Started

To run this demo, you will need to install [the Wasmer toolchain][install].

```console
$ curl https://get.wasmer.io -sSfL | sh
```

Now, start writing your PHP code.

```console
$ echo '<?php phpinfo(); ?>' > ./app/index.php
```

At this point, you would normally publish the package to WAPM.

```console
$ wasmer login $MY_API_TOKEN
$ wasmer publish .
```

You can also use `wasmer run-unstable` to test things locally.

```console
$ wasmer run-unstable .
INFO run: wasmer_wasix::runners::wcgi::runner: Starting the server address=127.0.0.1:8000 command_name="php"
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
