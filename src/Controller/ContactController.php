<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\AddContactFormType;
use App\Form\EditContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Contact::class);
        $contacts = $repository->findAll();

        return $this->render('home/home.html.twig', [
            'contacts' => $contacts
        ]);
    }

    /**
     * @Route("/show/{id}", name="show")
     */
    public function show($id)
    {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository(Contact::class)->findOneBy(['id' => $id]);

        return $this->render('details/details.html.twig', [
            'contact' => $contact
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function store(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $contact = new Contact();

        $form = $this->createForm(AddContactFormType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $file = $request->files->get('add_contact_form')['img_src'];

            $uploads_directory = $this->getParameter('uploads');

            $filename = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move(
                $uploads_directory,
                $filename
            );
            $contact->img_src = 'uploads/' . $filename;
            $em->persist($contact);
            $em->flush();

            $this->addFlash('success', 'Created contact with id ' . $contact->getId());
            return $this->redirect('/contact');
        }

        return $this->render('addContact/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/destroy/{id}", name="delete_contact")
     */
    public function destroy($id)
    {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository(Contact::class)->findOneBy(['id' => $id]);

        // Delete image from public_dir if it exists
        if (file_exists('uploads/' . $contact->img_src)) {
            unlink($contact->img_src);
        };

        $em->remove($contact);
        $em->flush();

        $this->addFlash('success', 'Deleted contact with id ' . $id);
        return $this->redirect('/contact');
    }

    /**
     * @Route("/edit/{id}", name="edit_contact")
     */
    public function edit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository(Contact::class)->findOneBy(['id' => $id]);

        $form = $this->createForm(EditContactFormType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $file = $request->files->get('edit_contact_form')['img_src'];

            $uploads_directory = $this->getParameter('uploads');

            $filename = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move(
                $uploads_directory,
                $filename
            );

            // Delete image from public_dir if it exists
            if (file_exists('uploads/' . $contact->img_src)) {
                unlink($contact->img_src);
            }
            $contact->img_src = 'uploads/' . $filename;
            $em->flush();

            $this->addFlash('success', 'Edited contact with id ' . $contact->getId());
            return $this->redirect('/contact');
        }

        return $this->render('editContact/edit.html.twig', [
            'form' => $form->createview()
        ]);
    }
}
