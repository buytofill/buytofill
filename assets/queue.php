<?
    if(!isset($_SERVER['_'])) exit;
    
    require '/home/vb7ju9mul43v/public_html/assets/aws.phar';

    $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));

    $stmt = $conn->prepare("SELECT DISTINCT f.email FROM `commit` c JOIN `filler` f ON c.uid = f.id WHERE c.email = 0");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt = $conn->prepare("UPDATE `commit` c JOIN `filler` f ON c.uid = f.id SET c.email = NULL WHERE c.email = 0");
    $stmt->execute();
    $batches = array_chunk(array_column($result->fetch_all(MYSQLI_ASSOC), 'email'), 50);

    $conn->close();

    $client = new Aws\Ses\SesClient(['region' => 'us-east-1', /*'debug' => true,*/ 'credentials' => ['key' => 'AKIAY2M4YSTP4HRRYZXB', 'secret' => getenv(AWS_SECRET)]]);

    foreach ($batches as $batch) {
        $destinations = [];

        foreach ($batch as $recipient) {
            $destinations[] = [
                'Destination' => [
                    'ToAddresses' => [$recipient],
                ]
            ];
        }

        $client->sendBulkTemplatedEmail([
            'Source' => 'BuyToFill <noreply@buytofill.com>',
            'Template' => 'blastCheckedIn0',
            'Destinations' => $destinations,
            'DefaultTemplateData' => json_encode(new \stdClass()),
        ]);

        usleep(500000);
    }
    
    exit;
    /*$client2 = new Aws\SesV2\SesV2Client(['region'=> 'us-east-1', 'credentials' =>['key'=> 'AKIAY2M4YSTP4HRRYZXB', 'secret'=> getenv(AWS_SECRET)]]);
    $client2->createEmailTemplate([
        'TemplateContent' => [
            'Html' =>   '<div style="background:#f3f4f6;border-radius:1rem;padding:2rem">
                            <div style="margin:auto;border-radius:.5rem;padding:1rem 2rem;background:#fff;width:600px">
                                <h2>Check out your newest sales!</h2>
                                <p>This email is a confirmation that your commitment is checked in. Make sure to request payment in order to receive your payout.</p>
                                <a href="https://buytofill.com/sales" style="text-decoration:none;background:#000;padding:.5rem 1rem;border-radius:.25rem;color:#fff">View Sales</a>
                                <hr style="margin-top:1rem">
                                <p style="font-size:.7rem">You are receiving this message because your items have been checked in with https://buytofill.com</p>
                            </div>
                        </div>',
            'Subject' => "We've received your packages!",
            'Text' => 'You have new sales on BuyToFill, visit https://buytofill.com/sales to request payment.',
        ],
        'TemplateName' => 'blastCheckedIn0',
    ]);*/
?>