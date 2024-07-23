{{R3M}}
{{$register = Package.R3m.Io.Account:Init:register()}}
{{if(!is.empty($register))}}
{{Package.R3m.Io.Account:Import:role.system()}}
{{$options = options()}}
/**
 // setup roles*
 // setup permissions*
 // setup jwt* (no patch, only force)
 // setup admin
 // setup user login (api.example.com)

 */
{{/if}}