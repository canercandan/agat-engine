#!/usr/bin/env sh

LOGIN=$1
PASSWD=$2
GROUP="agat"
HTDIGEST_PATH=$3

passwd_digest=

is_login()
{
    if [ "$LOGIN" = "" ]; then
	exit 0
    fi
}

is_passwd()
{
    if [ "$PASSWD" = "" ]; then
	exit 0
    fi
}

is_path()
{
    if [ "$HTDIGEST_PATH" = "" ]; then
	exit 0
    fi
}

passwd_generate()
{
    passwd_digest=$(echo -n $LOGIN":"$GROUP":"$PASSWD | openssl dgst -md5)
}

add_passwd()
{
    echo $LOGIN":"$GROUP":"$passwd_digest >> $HTDIGEST_PATH;
}

is_login
is_passwd
is_path
passwd_generate
add_passwd
