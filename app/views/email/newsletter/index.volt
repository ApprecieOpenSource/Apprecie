<table align="center" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
    <tr>
        <td></td>
        <td style="display: block;max-width: 600px;margin: 0 auto;clear: both;">
            <div style="padding: 10px 10px 5px 10px;max-width: 600px;margin: 0 auto;">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                    <tr>
                        <td>
                            <img style="border: none;max-height: 150px;max-width: 150px;"{logoAttribute} src="{logo}"/>
                        </td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </td>
        <td></td>
    </tr>
    </tbody>
</table>
<table align="center" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
    <tr>
        <td></td>
        <td style="display: block;max-width: 600px;margin: 0 auto;clear: both;background-color: #F3F3F4;">
            <div style="padding: 10px;max-width: 600px;margin: 0 auto;">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                    <tr>
                        <td>
                            <div style="font-weight: bold;font-style: italic;padding: 20px;font-family: {{font}};font-size: 16px;color: {{fontColor}};">
                                {heading}
                            </div>
                        </td>
                    </tr>
                    {content}
                    <tr>
                        <td>
                            <div style="color: {{fontColor}};padding: 10px;font-family: {{font}};font-size: 14px;">
                                {aboveLinkContent}
                            </div>
                            <div style="text-align: center;">
                                <!--[if mso]>
                                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{link}" style="height:40px;v-text-anchor:middle;width: 300px;" arcsize="10%" stroke="f" fillcolor="{{buttonPrimaryBackground}}">
                                    <w:anchorlock/>
                                    <center style="color: {{buttonPrimaryText}};font-family: {{font}};font-size: 16px;font-weight: bold;">
                                        {linkText}
                                    </center>
                                </v:roundrect>
                                <![endif]-->
                                <![if !mso]>
                                <table cellspacing="0" cellpadding="0" align="center" width="100%">
                                    <tr>
                                        <td>
                                            <div>
                                                <a href="{link}" style="text-decoration: none;text-align: center;font-weight: bold;font-family: {{font}};font-size: 16px;display: block;background-color: {{buttonPrimaryBackground}};color: {{buttonPrimaryText}};padding: 6px 12px;border-radius: 5px;">
                                                    {linkText}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <![endif]>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </td>
        <td></td>
    </tr>
    </tbody>
</table>
<table align="center" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tbody>
    <tr>
        <td></td>
        <td style="display: block;max-width: 600px;margin: 0 auto;clear: both;">
            <div style="margin: 0 auto;max-width: 600px;padding: 10px 0;font-family: {{font}};font-size: 10px;text-align: center;color: {{fontColor}};">
                <p style="color: #676A6C;">
                    <?= _g('To ensure that you can view your emails properly please add the domain "apprecie.com" to your Safe Senders List'); ?>
                </p>
                <p>
                    <a href="{termsLink}" target="_blank"><?= _g('Terms & Conditions'); ?></a>
                </p>
                <div>
                    <span style="color: #676A6C;"><?= _g('Powered by'); ?>&nbsp;</span><a href="http://apprecie.com/" target="_blank"><img style="border: none;vertical-align:middle;max-height: 20px;" src="{logoApprecie}"></a>
                </div>
            </div>
        </td>
        <td></td>
    </tr>
    </tbody>
</table>