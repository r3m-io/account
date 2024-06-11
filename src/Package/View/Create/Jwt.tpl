{{R3M}}
{{$response = Package.R3m.Io.Account:Main:account.create.jwt(flags(), options())}}
{{$response|object:'json'}}