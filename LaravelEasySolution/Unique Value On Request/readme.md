<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unique Maker</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>
        body {
            text-transform: capitalize;
        }

        pre {
            margin: 5px;
        }
    </style>
</head>

<body>
    <div style="margin: 0 auto">
        <h4>How to validate Unique data on Request Laravel</h4>
        <blockquote>
            <p>While Create</p>
            <pre>
                'email' => ['nullable', 'string','max:249', 
                Rule::unique('agency_customers')
                ->where(function ($query) use($email, $agencyId) {
                    return $query->where([
                        'email' => $email,
                        'agency_id' => $agencyId
                    ]);
                });
            </pre>

            <p>While Update</p>
            <pre>
                'email' => ['nullable', 'string','max:249', Rule::unique('agency_customers')->ignore($this->id)
                ->where(function ($query) use($email, $agencyId)
                {
                    return $query->where([
                        'email' => $email,
                        'agency_id' => $agencyId
                    ]);
                }),
              ],                
            </pre>
        </blockquote>
    </div>

</body>

</html>