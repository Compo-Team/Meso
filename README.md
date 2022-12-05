# Meso ↔️
<p align="center">
<img src="https://user-images.githubusercontent.com/107104144/197695795-ff29da7e-093a-44ac-ac49-5991f852636a.jpg" />
</p>

[![MIT License](https://img.shields.io/apm/l/atomic-design-ui.svg?)](https://github.com/tterb/atomic-design-ui/blob/master/LICENSEs)

[![telegram](https://badges.aleen42.com/src/telegram.svg)](https://t.me/CompoSoftware)

Meso is created to make database routines easy and solve some mysqli behaviors in php for once
# 🚀 About Team
Compo Team is focused on high-perfomance back-end services and network related topics.
Working on distributed computing.

## Support

For any question, sponsership, support, email amirmohammadfarhang83@gmail.com.

## How to use it?

First include/require the MESO and BASE file (composer with be available soon) then initiate it as below:
```
$Meso = new Meso(Address, User, Password, Database);
if(!$Meso->connect())
{
    print_r("Error: Service can not initialize \n reason: Database Connection Error");
    exit;
}
```
after that you can use it like :
```
$res = $this->Meso->readOne("users", $obj);
```
Meso->delete("registered", $obj)
```
Meso->query("UPDATE `users` SET `Password` = '$user->password' WHERE `username` = '$user->esername'")
```
the full documention will be avialable soon.

## Tech Stack

Only PHP nothing more, tested on centos, ubuntu, windows (php 7.4).

# Status ⚒️

still working to make it better.
