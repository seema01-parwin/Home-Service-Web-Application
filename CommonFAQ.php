<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>FAQ - Home Service Management</title>
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    rel="stylesheet"
  />
  <style>
    /* FAQ Page CSS */
    body {
      font-family: 'Poppins', sans-serif;
      background: #f9f9f9;
      color: #333;
      margin: 0;
      padding: 2rem 1rem;
    }

    h1 {
      text-align: center;
      margin-bottom: 2rem;
      color: #007bff;
    }

    .faq-container {
      max-width: 900px;
      margin: 0 auto;
      background: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgb(0 0 0 / 0.1);
    }

    .faq-item {
      border-bottom: 1px solid #eee;
      padding: 1rem 0;
    }

    .faq-question {
      font-weight: 600;
      font-size: 1.1rem;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      user-select: none;
      transition: color 0.3s ease;
    }
    .faq-question:hover {
      color: #007bff;
    }

    .faq-question i {
      font-size: 1.2rem;
      transition: transform 0.3s ease;
    }

    .faq-answer {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease, padding 0.3s ease;
      padding-left: 1rem;
      color: #555;
    }
    .faq-answer.open {
      padding-top: 0.75rem;
      padding-bottom: 1rem;
      max-height: 500px; /* enough for answer */
    }

    /* Responsive */
    @media (max-width: 600px) {
      body {
        padding: 1rem;
      }
      .faq-container {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <h1>Frequently Asked Questions</h1>
  <div class="faq-container">

    <!-- FAQ 1 -->
    <div class="faq-item">
      <div class="faq-question">
        How do I register as a customer or worker?
        <i class="fa-solid fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        You can register by clicking on the 'Register' link on the homepage and
        selecting either Customer or Worker registration forms. Fill in the
        required details and submit.
      </div>
    </div>

    <!-- FAQ 2 -->
    <div class="faq-item">
      <div class="faq-question">
        How do I book a service as a customer?
        <i class="fa-solid fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        After logging in as a customer, navigate to 'Book a Service', select the
        service category, preferred date/time, and location, then confirm your
        booking.
      </div>
    </div>

    <!-- FAQ 3 -->
    <div class="faq-item">
      <div class="faq-question">
        How can workers upload proof of completed work?
        <i class="fa-solid fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        Workers can upload work proof images or documents from their dashboard
        after completing a booking. Navigate to 'Upload Work Proof', select the
        booking, and upload the files.
      </div>
    </div>

    <!-- FAQ 4 -->
    <div class="faq-item">
      <div class="faq-question">
        What should I do if I forget my password?
        <i class="fa-solid fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        Click on the 'Forgot Password' link on the login page and follow the
        instructions to reset your password via your registered email.
      </div>
    </div>

    <!-- FAQ 5 -->
    <div class="faq-item">
      <div class="faq-question">
        How do I rate and review a worker after service completion?
        <i class="fa-solid fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        Customers can rate and review workers from their booking history page
        once the service status is marked as completed.
      </div>
    </div>

    <!-- FAQ 6 -->
    <div class="faq-item">
      <div class="faq-question">
        How can workers manage their availability?
        <i class="fa-solid fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        Workers can update their availability calendar and working hours from
        their dashboard profile section to let customers know when they are free
        for bookings.
      </div>
    </div>

  </div>

  <script>
    // Accordion toggle
    document.querySelectorAll('.faq-question').forEach((question) => {
      question.addEventListener('click', () => {
        const answer = question.nextElementSibling;

        // Close other answers
        document.querySelectorAll('.faq-answer.open').forEach((openAnswer) => {
          if (openAnswer !== answer) {
            openAnswer.classList.remove('open');
            openAnswer.previousElementSibling.querySelector('i').style.transform =
              'rotate(0deg)';
          }
        });

        // Toggle current answer
        answer.classList.toggle('open');
        const icon = question.querySelector('i');
        if (answer.classList.contains('open')) {
          icon.style.transform = 'rotate(180deg)';
        } else {
          icon.style.transform = 'rotate(0deg)';
        }
      });
    });
  </script>
</body>
</html>
