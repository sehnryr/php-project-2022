<?php

/**
 * PHP version 8.1.0
 * 
 * @author Maël Grellier Neau <mael.grelneau@gmail.com>
 * @author Maxence Laurent <nano0@duck.com>
 * @author Youn Mélois <youn@melois.dev>
 */

/**
 * This exception is thrown when the authentication failed.
 */
class AuthenticationException extends Exception
{
}

/**
 * This will be thrown when trying to create a user with a duplicate email.
 */
class DuplicateEmailException extends Exception
{
}
