<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
    <html>
        <head>
            <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
            <title>souÁgil - soluções ágeis</title>
            <style>
                body {
                    background: #f8d54a;
                }
                body div {
                    width: 570px;
                    margin: 130px auto 0;
                }
                a img {
                    border: none;
                }
            </style>
        </head>
        <body>
            <h1>Repositório de Plugins souÁgil</h1>
            <dl>
                <xsl:for-each select="plugins/plugin">
                    <dt><xsl:value-of select="description"/></dt>
                    <dd><a href="{url}"><xsl:value-of select="name"/></a></dd>
                </xsl:for-each>
            </dl>
        </body>
    </html>
</xsl:template>

</xsl:stylesheet>