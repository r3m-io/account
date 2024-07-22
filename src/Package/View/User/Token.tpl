{{R3M}}
{{$response = Package.R3m.Io.Account:Main:account.user.token(flags(), options())}}
{{$response|object:'json'}}