{{R3M}}
{{$response = Package.R3m.Io.Account:Main:user.token(flags(), options())}}
{{$response|object:'json'}}