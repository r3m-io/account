{{R3M}}
{{$response = Package.R3m.Io.Account:Main:user.create.login(flags(), options())}}
{{$response|object:'json'}}