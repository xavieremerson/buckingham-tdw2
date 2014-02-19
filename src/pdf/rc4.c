/*
  Copyright (C) 2002 CenterSys Group, Inc.  All rights reserved.
  support@centersys.com
*/
/*
 * "$Id: rc4.c,v 1.3 2000/06/05 17:55:45 mike Exp $"
 *
 *
 * Contents:
 *
 *   rc4_init()    - Initialize an RC4 context with the specified key.
 *   rc4_encrypt() - Encrypt the given buffer.
 */

#include "rc4.h"


/*
 * 'rc4_init()' - Initialize an RC4 context with the specified key.
 */

void
rc4_init(rc4_context_t       *text,	/* IO - Context */
         const unsigned char *key,	/* I - Key */
         unsigned            keylen)	/* I - Length of key */
{
  int		i, j;			/* Looping vars */
  unsigned char	tmp;			/* Temporary variable */


 /*
  * Fill in linearly s0=0, s1=1, ...
  */

  for (i = 0; i < 256; i ++)
    text->sbox[i] = i;

  for (i = 0, j = 0; i < 256; i ++)
  {
   /*
    * j = (j + Si + Ki) mod 256
    */

    j = (j + text->sbox[i] + key[i % keylen]) & 255;

   /*
    * Swap Si and Sj...
    */

    tmp           = text->sbox[i];
    text->sbox[i] = text->sbox[j];
    text->sbox[j] = tmp;
  }

 /*
  * Initialized counters to 0 and return...
  */

  text->i = 0;
  text->j = 0;
}


/*
 * 'rc4_encrypt()' - Encrypt the given buffer.
 */

void
rc4_encrypt(rc4_context_t       *text,		/* I - Context */
	    const unsigned char *input,		/* I - Input buffer */
	    unsigned char       *output,	/* O - Output buffer */
	    unsigned            len)		/* I - Size of buffers */
{
  unsigned char		tmp;			/* Swap variable */
  int			i, j;			/* Looping vars */
  int			t;			/* Current S box */


 /*
  * Loop through the entire buffer...
  */

  i = text->i;
  j = text->j;

  while (len > 0)
  {
   /*
    * Get the next S box indices...
    */

    i = (i + 1) & 255;
    j = (j + text->sbox[i]) & 255;

   /*
    * Swap Si and Sj...
    */

    tmp           = text->sbox[i];
    text->sbox[i] = text->sbox[j];
    text->sbox[j] = tmp;

   /*
    * Get the S box index for this byte...
    */

    t = (text->sbox[i] + text->sbox[j]) & 255;

   /*
    * Encrypt using the S box...
    */

    *output++ = *input++ ^ text->sbox[t];
    len --;
  }

 /*
  * Copy current S box indices back to context...
  */

  text->i = i;
  text->j = j;
}


/*
 * End of "$Id: rc4.c,v 1.3 2000/06/05 17:55:45 mike Exp $".
 */
