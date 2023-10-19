# Example mathematical proofs

This directory contains example mathematical proofs and other arguments as samples and test cases for the proof library.  

Each must define three variables:

1. a variable `thm` to hold a statement of the theorem.
2. a variable `proof_steps`, which is a list of strings.
3. a variable `proof_ans` which represents the proof.

For example, if we have theorem  "\(n\) is odd if and only if \(n^2\) is odd", then we define its proof as a tree using

    proof_ans:proof_iff(proof(1,2,3,4,5),proof(6,7,8,9,10,11));

Really this means the proof is an if and only if proof (`proof_iff`) with two blocks, themselves proofs.  The sub-proofs are the most basic proof type, which is a list of steps, e.g. `proof(1,2,3,4,5)`.   The steps of a proof are integer indexes to the `proof_steps` list.  We deal with indexes, not strings, to simplify the representation and manipulation of the proof trees.
