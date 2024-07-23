{{R3M}}
{{$response = Package.R3m.Io.Account:Main:setup.jwt(flags(), options())}}
{{$response|object:'json'}}